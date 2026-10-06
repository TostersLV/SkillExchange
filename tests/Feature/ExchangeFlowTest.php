<?php

use App\Models\PostOffer;
use App\Models\User;
use App\PostOfferStatus;
use App\PostStatus;

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

test('the post owner can reject an offer', function () {
    $offer = PostOffer::factory()->create();

    $this->actingAs($offer->post->user)->delete(route('posts.offers.reject', $offer));

    $this->assertModelMissing($offer);
});

test('the sender can cancel their pending offer', function () {
    $offer = PostOffer::factory()->create();

    $this->actingAs($offer->user)->delete(route('posts.offers.cancel', $offer));

    $this->assertModelMissing($offer);
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

test('a review updates the other user\'s reputation', function () {
    $offer = PostOffer::factory()->accepted()->create();
    $offer->post->update(['status' => PostStatus::COMPLETED]);
    $author = $offer->post->user;

    $this->actingAs($offer->user)
        ->patch(route('posts.progress.review', $offer), ['rating' => 4])
        ->assertRedirect();

    $this->assertDatabaseHas('reviews', [
        'reviewer_id' => $offer->user_id,
        'reviewee_id' => $author->id,
        'review' => 4,
    ]);
    expect((float) $author->fresh()->reputation)->toBe(4.0);
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
