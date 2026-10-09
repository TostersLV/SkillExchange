<?php

use App\Models\Message;
use App\Models\PostOffer;
use App\Models\User;
use Livewire\Livewire;

test('a participant can send a message', function () {
    $offer = PostOffer::factory()->accepted()->create();

    Livewire::actingAs($offer->user)
        ->test('posts.progress-chat', ['offer' => $offer])
        ->set('body', 'Hi there!')
        ->call('sendMessage')
        ->assertHasNoErrors()
        ->assertSet('body', '')
        ->assertSee('Hi there!');

    $this->assertDatabaseHas('messages', [
        'post_offer_id' => $offer->id,
        'user_id' => $offer->user_id,
        'body' => 'Hi there!',
    ]);
});

test('an empty message is not sent', function () {
    $offer = PostOffer::factory()->accepted()->create();

    Livewire::actingAs($offer->user)
        ->test('posts.progress-chat', ['offer' => $offer])
        ->set('body', '')
        ->call('sendMessage')
        ->assertHasErrors(['body' => 'required']);

    expect($offer->messages()->count())->toBe(0);
});

test('a message cannot be longer than 1000 characters', function () {
    $offer = PostOffer::factory()->accepted()->create();

    Livewire::actingAs($offer->user)
        ->test('posts.progress-chat', ['offer' => $offer])
        ->set('body', str_repeat('a', 1001))
        ->call('sendMessage')
        ->assertHasErrors(['body' => 'max']);
});

test('strangers cannot send messages in someone else\'s exchange', function () {
    $offer = PostOffer::factory()->accepted()->create();

    Livewire::actingAs(User::factory()->create())
        ->test('posts.progress-chat', ['offer' => $offer])
        ->set('body', 'Sneaky')
        ->call('sendMessage')
        ->assertForbidden();

    expect($offer->messages()->count())->toBe(0);
});

test('a long chat opens with the newest 50 messages', function () {
    $offer = PostOffer::factory()->accepted()->create();
    foreach (range(1, 60) as $number) {
        Message::factory()->for($offer, 'postOffer')->for($offer->user)->create(['body' => "Message number {$number}."]);
    }

    Livewire::actingAs($offer->user)
        ->test('posts.progress-chat', ['offer' => $offer])
        ->assertSee('Message number 60.')
        ->assertSee('Message number 11.')
        ->assertDontSee('Message number 10.')
        ->assertSee('Load older messages');
});

test('scrolling up loads the older messages until the first one', function () {
    $offer = PostOffer::factory()->accepted()->create();
    foreach (range(1, 60) as $number) {
        Message::factory()->for($offer, 'postOffer')->for($offer->user)->create(['body' => "Message number {$number}."]);
    }

    Livewire::actingAs($offer->user)
        ->test('posts.progress-chat', ['offer' => $offer])
        ->call('loadOlder')
        ->assertSee('Message number 1.')
        ->assertSeeInOrder(['Message number 1.', 'Message number 2.', 'Message number 60.'])
        ->assertDontSee('Load older messages');
});

test('polling shows a new message from the other participant', function () {
    $offer = PostOffer::factory()->accepted()->create();

    $chat = Livewire::actingAs($offer->user)->test('posts.progress-chat', ['offer' => $offer]);

    Message::factory()->for($offer, 'postOffer')->for($offer->post->user)->create(['body' => 'See you on Monday!']);

    $chat->call('checkForNewMessages')->assertSee('See you on Monday!');
});

test('polling does not re-render when there is nothing new', function () {
    $offer = PostOffer::factory()->accepted()->create();
    Message::factory()->for($offer, 'postOffer')->for($offer->user)->create();

    Livewire::actingAs($offer->user)
        ->test('posts.progress-chat', ['offer' => $offer])
        ->call('checkForNewMessages')
        ->assertNoRedirect()
        ->tap(fn ($chat) => expect($chat->effects['html'] ?? null)->toBeNull());
});

test('a user can send up to 25 messages a minute', function () {
    $offer = PostOffer::factory()->accepted()->create();
    $chat = Livewire::actingAs($offer->user)->test('posts.progress-chat', ['offer' => $offer]);

    foreach (range(1, 25) as $number) {
        $chat->set('body', "Message {$number}")->call('sendMessage')->assertHasNoErrors();
    }

    expect($offer->messages()->count())->toBe(25);
});

test('the 26th message within a minute is refused and kept in the box', function () {
    $offer = PostOffer::factory()->accepted()->create();
    $chat = Livewire::actingAs($offer->user)->test('posts.progress-chat', ['offer' => $offer]);

    foreach (range(1, 25) as $number) {
        $chat->set('body', "Message {$number}")->call('sendMessage');
    }

    $chat->set('body', 'One too many')
        ->call('sendMessage')
        ->assertHasErrors('body')
        ->assertSee('sending messages too fast')
        ->assertSet('body', 'One too many');

    expect($offer->messages()->count())->toBe(25);
});

test('the limit counts messages across all of the user\'s chats', function () {
    $sender = User::factory()->create();
    $firstChat = PostOffer::factory()->accepted()->for($sender)->create();
    $secondChat = PostOffer::factory()->accepted()->for($sender)->create();

    $chat = Livewire::actingAs($sender)->test('posts.progress-chat', ['offer' => $firstChat]);
    foreach (range(1, 25) as $number) {
        $chat->set('body', "Message {$number}")->call('sendMessage');
    }

    Livewire::actingAs($sender)
        ->test('posts.progress-chat', ['offer' => $secondChat])
        ->set('body', 'Hello')
        ->call('sendMessage')
        ->assertHasErrors('body');

    expect($secondChat->messages()->count())->toBe(0);
});

test('sending works again after a minute', function () {
    $offer = PostOffer::factory()->accepted()->create();
    $chat = Livewire::actingAs($offer->user)->test('posts.progress-chat', ['offer' => $offer]);

    foreach (range(1, 25) as $number) {
        $chat->set('body', "Message {$number}")->call('sendMessage');
    }

    $this->travel(61)->seconds();

    $chat->set('body', 'Back again')->call('sendMessage')->assertHasNoErrors();

    expect($offer->messages()->count())->toBe(26);
});

test('one user reaching the limit does not block the other participant', function () {
    $offer = PostOffer::factory()->accepted()->create();
    $chat = Livewire::actingAs($offer->user)->test('posts.progress-chat', ['offer' => $offer]);
    foreach (range(1, 25) as $number) {
        $chat->set('body', "Message {$number}")->call('sendMessage');
    }

    Livewire::actingAs($offer->post->user)
        ->test('posts.progress-chat', ['offer' => $offer])
        ->set('body', 'My turn')
        ->call('sendMessage')
        ->assertHasNoErrors();

    expect($offer->messages()->where('user_id', $offer->post->user_id)->count())->toBe(1);
});
