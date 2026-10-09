<?php

use App\Models\Post;
use App\Models\PostOffer;
use App\Models\User;
use App\PostOfferStatus;
use App\PostStatus;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

test('the post owner can accept an offer and the post becomes in progress', function () {
    $offer = PostOffer::factory()->create();

    $this->actingAs($offer->post->user)
        ->patch(route('post.offers.accept', $offer))
        ->assertRedirect();

    expect($offer->fresh()->status)->toBe(PostOfferStatus::ACCEPTED);
    expect($offer->post->fresh()->status)->toBe(PostStatus::IN_PROGRESS);
});

test('other users cannot accept an offer', function () {
    $offer = PostOffer::factory()->create();

    $this->actingAs(User::factory()->create())
        ->patch(route('post.offers.accept', $offer))
        ->assertForbidden();

    expect($offer->post->fresh()->status)->toBe(PostStatus::AVAILABLE);
});

test('declining an offer keeps it as declined', function () {
    $offer = PostOffer::factory()->create();

    $this->actingAs($offer->post->user)->delete(route('posts.offers.reject', $offer));

    expect($offer->fresh()->status)->toBe(PostOfferStatus::REJECTED);
});

test('the sender can withdraw their pending offer and it leaves their requests', function () {
    $offer = PostOffer::factory()->create();

    $this->actingAs($offer->user)->delete(route('posts.offers.cancel', $offer));

    expect($offer->fresh()->status)->toBe(PostOfferStatus::WITHDRAWN);

    $this->get(route('posts.requests'))->assertDontSee($offer->post->offering_skill);
});

test('the post completes only after both users confirm', function () {
    $offer = PostOffer::factory()->accepted()->create();

    $this->actingAs($offer->post->user)->patch(route('posts.progress.complete', $offer));
    expect($offer->post->fresh()->status)->toBe(PostStatus::IN_PROGRESS);

    $this->actingAs($offer->user)->patch(route('posts.progress.complete', $offer));
    expect($offer->post->fresh()->status)->toBe(PostStatus::COMPLETED);
});

test('a user cannot confirm completion twice', function () {
    $offer = PostOffer::factory()->accepted()->create();

    $this->actingAs($offer->user)->patch(route('posts.progress.complete', $offer));

    $this->actingAs($offer->user)
        ->patch(route('posts.progress.complete', $offer))
        ->assertForbidden();
});

test('users cannot review before the exchange is completed', function () {
    $offer = PostOffer::factory()->accepted()->create();

    $this->actingAs($offer->user)
        ->patch(route('posts.progress.review', $offer), ['rating' => 5])
        ->assertForbidden();
});

test('a user\'s rating is the average of the reviews they received', function () {
    $author = User::factory()->create();
    $firstExchange = PostOffer::factory()->for(Post::factory()->for($author)->state(['status' => PostStatus::COMPLETED]))->create(['status' => PostOfferStatus::ACCEPTED]);
    $secondExchange = PostOffer::factory()->for(Post::factory()->for($author)->state(['status' => PostStatus::COMPLETED]))->create(['status' => PostOfferStatus::ACCEPTED]);

    $this->actingAs($firstExchange->user)
        ->patch(route('posts.progress.review', $firstExchange), ['rating' => 5])
        ->assertRedirect();

    $this->assertDatabaseHas('reviews', [
        'reviewer_id' => $firstExchange->user_id,
        'reviewee_id' => $author->id,
        'review' => 5,
    ]);

    $this->actingAs($secondExchange->user)
        ->patch(route('posts.progress.review', $secondExchange), ['rating' => 2]);

    expect($author->fresh()->reputation)->toBe(3.5);
    expect(User::factory()->create()->reputation)->toBeNull();
});

test('reviewing the same exchange twice is refused and keeps one review', function () {
    $offer = PostOffer::factory()->for(Post::factory()->state(['status' => PostStatus::COMPLETED]))->create(['status' => PostOfferStatus::ACCEPTED]);

    $this->actingAs($offer->user)->patch(route('posts.progress.review', $offer), ['rating' => 5]);

    $this->patch(route('posts.progress.review', $offer), ['rating' => 1])->assertForbidden();

    expect($offer->reviews()->count())->toBe(1);
    expect($offer->post->user->fresh()->reputation)->toBe(5.0);
});

test('a rating must be between 1 and 5', function () {
    $offer = PostOffer::factory()->accepted()->create();
    $offer->post->update(['status' => PostStatus::COMPLETED]);

    $this->actingAs($offer->user)
        ->patch(route('posts.progress.review', $offer), ['rating' => 9])
        ->assertSessionHasErrors('rating');
});

test('strangers cannot open an exchange page', function () {
    $offer = PostOffer::factory()->accepted()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('posts.progress.show', $offer))
        ->assertForbidden();
});

test('the offers page shows each offer as a trade with accept and decline actions', function () {
    $offer = PostOffer::factory()->create();
    $offer->post->update(['offering_skill' => 'Guitar lessons', 'looking_skill' => 'German classes']);

    $this->actingAs($offer->post->user)
        ->get(route('posts.offers'))
        ->assertOk()
        ->assertSeeInOrder(['Guitar lessons', 'German classes'])
        ->assertSee($offer->user->username)
        ->assertSee('Accept swap')
        ->assertSee('Decline');
});

test('the requests page shows offers the user sent', function () {
    $offer = PostOffer::factory()->create();

    $this->actingAs($offer->user)
        ->get(route('posts.requests'))
        ->assertOk()
        ->assertSee($offer->post->offering_skill)
        ->assertSee('Cancel request');
});

test('the in progress page lists the user\'s open exchanges with the other member', function () {
    $offer = PostOffer::factory()->accepted()->create();

    $this->actingAs($offer->post->user)
        ->get(route('posts.progress'))
        ->assertOk()
        ->assertSee($offer->post->offering_skill)
        ->assertSee($offer->user->username);
});

test('a participant can open the exchange page', function () {
    $offer = PostOffer::factory()->accepted()->create();

    $this->actingAs($offer->user)
        ->get(route('posts.progress.show', $offer))
        ->assertOk()
        ->assertSee($offer->post->offering_skill)
        ->assertSee('Mark as complete');
});

test('one participant asking to cancel keeps the exchange going', function () {
    $offer = PostOffer::factory()->accepted()->create();

    $this->actingAs($offer->post->user)
        ->patch(route('posts.progress.cancel', $offer))
        ->assertRedirect();

    expect($offer->fresh()->status)->toBe(PostOfferStatus::ACCEPTED);
    expect($offer->post->fresh()->status)->toBe(PostStatus::IN_PROGRESS);
});

test('the exchange is cancelled once both participants agree and the post reopens with its other offers', function () {
    $accepted = PostOffer::factory()->accepted()->create();
    $waiting = PostOffer::factory()->for($accepted->post)->create();
    $accepted->cancelOffers()->create(['user_id' => $accepted->user_id]);

    $this->actingAs($accepted->post->user)
        ->patch(route('posts.progress.cancel', $accepted))
        ->assertRedirect(route('posts.offers'));

    expect($accepted->fresh()->status)->toBe(PostOfferStatus::CANCELLED);
    expect($accepted->post->fresh()->status)->toBe(PostStatus::AVAILABLE);
    expect($waiting->fresh()->status)->toBe(PostOfferStatus::PENDING);

    $this->patch(route('post.offers.accept', $waiting))->assertRedirect();

    expect($waiting->fresh()->status)->toBe(PostOfferStatus::ACCEPTED);
});

test('a participant cannot agree to cancel twice', function () {
    $offer = PostOffer::factory()->accepted()->create();
    $offer->cancelOffers()->create(['user_id' => $offer->user_id]);

    $this->actingAs($offer->user)
        ->patch(route('posts.progress.cancel', $offer))
        ->assertForbidden();

    expect($offer->fresh()->status)->toBe(PostOfferStatus::ACCEPTED);
});

test('strangers cannot cancel an exchange', function () {
    $offer = PostOffer::factory()->accepted()->create();

    $this->actingAs(User::factory()->create())
        ->patch(route('posts.progress.cancel', $offer))
        ->assertForbidden();

    expect($offer->cancelOffers()->exists())->toBeFalse();
});

test('the exchange page shows who is waiting on the cancel request', function () {
    $offer = PostOffer::factory()->accepted()->create();
    $offer->cancelOffers()->create(['user_id' => $offer->user_id]);

    $this->actingAs($offer->user)
        ->get(route('posts.progress.show', $offer))
        ->assertSee('to agree to cancel');

    $this->actingAs($offer->post->user)
        ->get(route('posts.progress.show', $offer))
        ->assertSee('wants to cancel this exchange');
});

test('the post owner can accept another offer while the post is in progress', function () {
    $first = PostOffer::factory()->accepted()->create();
    $second = PostOffer::factory()->for($first->post)->create();

    $this->actingAs($first->post->user)
        ->patch(route('post.offers.accept', $second))
        ->assertRedirect();

    expect($first->fresh()->status)->toBe(PostOfferStatus::ACCEPTED);
    expect($second->fresh()->status)->toBe(PostOfferStatus::ACCEPTED);
});

test('offers cannot be accepted once the post is completed', function () {
    $offer = PostOffer::factory()->for(Post::factory()->state(['status' => PostStatus::COMPLETED]))->create();

    $this->actingAs($offer->post->user)
        ->patch(route('post.offers.accept', $offer))
        ->assertForbidden();

    expect($offer->fresh()->status)->toBe(PostOfferStatus::PENDING);
});

test('completing one exchange closes the other exchanges and offers on the post', function () {
    $chosen = PostOffer::factory()->accepted()->create();
    $otherExchange = PostOffer::factory()->for($chosen->post)->create(['status' => PostOfferStatus::ACCEPTED]);
    $waiting = PostOffer::factory()->for($chosen->post)->create();
    $chosen->completeOffers()->create(['user_id' => $chosen->user_id]);

    $this->actingAs($chosen->post->user)
        ->patch(route('posts.progress.complete', $chosen))
        ->assertRedirect();

    expect($chosen->post->fresh()->status)->toBe(PostStatus::COMPLETED);
    expect($chosen->fresh()->status)->toBe(PostOfferStatus::ACCEPTED);
    expect($otherExchange->fresh()->status)->toBe(PostOfferStatus::CANCELLED);
    expect($waiting->fresh()->status)->toBe(PostOfferStatus::REJECTED);
});

test('cancelling one of several exchanges keeps the post in progress', function () {
    $cancelled = PostOffer::factory()->accepted()->create();
    $stillRunning = PostOffer::factory()->for($cancelled->post)->create(['status' => PostOfferStatus::ACCEPTED]);
    $cancelled->cancelOffers()->create(['user_id' => $cancelled->user_id]);

    $this->actingAs($cancelled->post->user)
        ->patch(route('posts.progress.cancel', $cancelled))
        ->assertRedirect();

    expect($cancelled->fresh()->status)->toBe(PostOfferStatus::CANCELLED);
    expect($stillRunning->fresh()->status)->toBe(PostOfferStatus::ACCEPTED);
    expect($cancelled->post->fresh()->status)->toBe(PostStatus::IN_PROGRESS);
});

test('the requests page explains why an offer ended', function () {
    $sender = User::factory()->create();
    $declined = PostOffer::factory()->for($sender)->create(['status' => PostOfferStatus::REJECTED]);
    $calledOff = PostOffer::factory()->for($sender)->create(['status' => PostOfferStatus::CANCELLED]);
    $calledOff->cancelOffers()->create(['user_id' => $sender->id]);
    $calledOff->cancelOffers()->create(['user_id' => $calledOff->post->user_id]);
    $lostOut = PostOffer::factory()->for($sender)
        ->for(Post::factory()->state(['status' => PostStatus::COMPLETED]))
        ->create(['status' => PostOfferStatus::CANCELLED]);

    $this->actingAs($sender)
        ->get(route('posts.requests'))
        ->assertOk()
        ->assertSee($declined->post->user->username.' declined your request.')
        ->assertSee('This exchange was cancelled.')
        ->assertSee($lostOut->post->user->username.' completed this swap with another member.');
});

test('the sender can dismiss an ended offer without deleting it', function () {
    $offer = PostOffer::factory()->create(['status' => PostOfferStatus::REJECTED]);

    $this->actingAs($offer->user)
        ->patch(route('posts.requests.dismiss', $offer))
        ->assertRedirect();

    expect($offer->fresh()->dismissed_at)->not->toBeNull();

    $this->get(route('posts.requests'))->assertDontSee('declined your request');
});

test('only the sender can dismiss, and only offers that ended', function () {
    $declined = PostOffer::factory()->create(['status' => PostOfferStatus::REJECTED]);
    $pending = PostOffer::factory()->for($declined->user)->create();

    $this->actingAs($declined->post->user)
        ->patch(route('posts.requests.dismiss', $declined))
        ->assertForbidden();

    $this->actingAs($pending->user)
        ->patch(route('posts.requests.dismiss', $pending))
        ->assertForbidden();

    expect($declined->fresh()->dismissed_at)->toBeNull();
});

test('a declined user sees why on the post and can propose again', function (PostOfferStatus $status, string $badge) {
    $offer = PostOffer::factory()->create(['status' => $status]);

    $this->actingAs($offer->user)
        ->get(route('posts.show', $offer->post))
        ->assertOk()
        ->assertSee($badge)
        ->assertSee('Propose again');

    Livewire::test('posts.send-offer', ['post' => $offer->post])
        ->call('sendOffer')
        ->assertSee('Offer sent');

    expect($offer->post->offers()->where('status', PostOfferStatus::PENDING)->count())->toBe(1);
    expect($offer->fresh()->status)->toBe($status);
})->with([
    'declined' => [PostOfferStatus::REJECTED, 'Your offer was declined'],
    'cancelled' => [PostOfferStatus::CANCELLED, 'Your exchange was cancelled'],
]);

test('only the declined user sees the declined badge on explore', function () {
    $offer = PostOffer::factory()->create(['status' => PostOfferStatus::REJECTED]);

    $this->actingAs($offer->user);
    Livewire::test('posts.search-and-filter')
        ->assertSee('Declined')
        ->assertSee('Propose again');

    $this->actingAs(User::factory()->create());
    Livewire::test('posts.search-and-filter')
        ->assertDontSee('Declined')
        ->assertSee('Propose swap');
});

test('sending an offer twice creates only one pending offer', function () {
    $post = Post::factory()->create();

    $this->actingAs(User::factory()->create());

    Livewire::test('posts.send-offer', ['post' => $post])
        ->call('sendOffer')
        ->call('sendOffer');

    expect($post->offers()->count())->toBe(1);
});

test('no offer is created when the post stopped being available', function (PostStatus $status) {
    $component = Livewire::actingAs(User::factory()->create())
        ->test('posts.send-offer', ['post' => $post = Post::factory()->create()]);

    $post->update(['status' => $status]);

    $component->call('sendOffer');

    expect($post->offers()->count())->toBe(0);
})->with([PostStatus::IN_PROGRESS, PostStatus::CANCELLED]);

test('a user can have ended offers and a new pending one on the same post', function () {
    $declined = PostOffer::factory()->create(['status' => PostOfferStatus::REJECTED]);
    PostOffer::factory()->for($declined->post)->for($declined->user)->create(['status' => PostOfferStatus::CANCELLED]);

    $this->actingAs($declined->user);
    Livewire::test('posts.send-offer', ['post' => $declined->post])->call('sendOffer');

    expect($declined->post->offers()->where('status', PostOfferStatus::PENDING)->count())->toBe(1);
});

test('a participant can undo marking the exchange as complete', function () {
    $offer = PostOffer::factory()->accepted()->create();
    $offer->completeOffers()->create(['user_id' => $offer->user_id]);

    $this->actingAs($offer->user)
        ->get(route('posts.progress.show', $offer))
        ->assertSee('Undo mark as complete');

    $this->delete(route('posts.progress.complete.undo', $offer))->assertRedirect();

    expect($offer->hasBeenCompletedBy($offer->user))->toBeFalse();

    $this->get(route('posts.progress.show', $offer))->assertDontSee('Undo mark as complete');
});

test('completion cannot be undone once both participants confirmed', function () {
    $offer = PostOffer::factory()->accepted()->create();
    $offer->completeOffers()->create(['user_id' => $offer->user_id]);
    $offer->completeOffers()->create(['user_id' => $offer->post->user_id]);
    $offer->post->update(['status' => PostStatus::COMPLETED]);

    $this->actingAs($offer->user)
        ->delete(route('posts.progress.complete.undo', $offer))
        ->assertForbidden();

    expect($offer->completeOffers()->count())->toBe(2);
});

test('a participant can withdraw their cancel request', function () {
    $offer = PostOffer::factory()->accepted()->create();
    $offer->cancelOffers()->create(['user_id' => $offer->user_id]);

    $this->actingAs($offer->user)
        ->get(route('posts.progress.show', $offer))
        ->assertSee('Withdraw cancel request');

    $this->delete(route('posts.progress.cancel.withdraw', $offer))->assertRedirect();

    expect($offer->hasRequestedCancelBy($offer->user))->toBeFalse();
    expect($offer->fresh()->status)->toBe(PostOfferStatus::ACCEPTED);
});

test('only the participant who asked can withdraw a cancel request', function () {
    $offer = PostOffer::factory()->accepted()->create();
    $offer->cancelOffers()->create(['user_id' => $offer->user_id]);

    $this->actingAs($offer->post->user)
        ->delete(route('posts.progress.cancel.withdraw', $offer))
        ->assertForbidden();

    $this->actingAs(User::factory()->create())
        ->delete(route('posts.progress.cancel.withdraw', $offer))
        ->assertForbidden();

    expect($offer->hasRequestedCancelBy($offer->user))->toBeTrue();
});

test('the in progress page uses the same number of queries however many exchanges there are', function () {
    $user = User::factory()->create();
    $countQueries = function () use ($user): int {
        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->actingAs($user)->get(route('posts.progress'))->assertOk();

        return count(DB::getQueryLog());
    };

    PostOffer::factory()->accepted()->for($user)->create();
    $queriesWithOne = $countQueries();

    PostOffer::factory()->accepted()->for($user)->count(4)->create();
    $queriesWithFive = $countQueries();

    expect($queriesWithFive)->toBe($queriesWithOne);
});
