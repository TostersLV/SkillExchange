<?php

use App\Models\Post;
use App\Models\PostOffer;
use App\Models\Review;
use App\Models\User;
use App\PostOfferStatus;
use App\PostStatus;

test('the profile shows the member\'s reviews and completed swaps', function () {
    $member = User::factory()->create();
    $completedPost = Post::factory()->for($member)->create(['status' => PostStatus::COMPLETED]);
    $offer = PostOffer::factory()->for($completedPost)->create(['status' => PostOfferStatus::ACCEPTED]);
    Review::create(['post_offer_id' => $offer->id, 'reviewer_id' => $offer->user_id, 'reviewee_id' => $member->id, 'review' => 5]);

    $response = $this->actingAs(User::factory()->create())->get(route('profile.show', $member));

    $response->assertOk();
    $response->assertViewHas('reviewsCount', 1);
    $response->assertViewHas('completedExchangesCount', 1);
});

test('visitors only see a member\'s open exchanges', function () {
    $member = User::factory()->create();
    Post::factory()->for($member)->create(['offering_skill' => 'Guitar lessons']);
    Post::factory()->for($member)->create(['offering_skill' => 'Baking', 'status' => PostStatus::COMPLETED]);

    $this->actingAs(User::factory()->create())
        ->get(route('profile.show', $member))
        ->assertSee('Guitar lessons')
        ->assertDontSee('Baking')
        ->assertDontSee('Edit profile');
});

test('members see an edit button on their own profile', function () {
    $member = User::factory()->create();

    $this->actingAs($member)
        ->get(route('profile.show', $member))
        ->assertSee('Edit profile');
});
