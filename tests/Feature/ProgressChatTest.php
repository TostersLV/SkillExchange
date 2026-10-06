<?php

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
