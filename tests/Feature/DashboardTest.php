<?php

use App\Models\Post;
use App\Models\PostOffer;
use App\Models\Review;
use App\Models\User;
use App\PostOfferStatus;
use App\PostStatus;

test('guests visiting the home page are redirected to the landing page', function () {
    $response = $this->get(route('home'));
    $response->assertRedirect(route('welcome'));
});

test('guests visiting other protected pages are redirected to the login page', function () {
    $response = $this->get(route('posts.create'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the home page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('home'));
    $response->assertOk();
});

test('the home page shows the user\'s pending offers and active exchanges', function () {
    $user = User::factory()->create();
    $myPost = Post::factory()->for($user)->create();

    PostOffer::factory()->count(2)->for($myPost)->create();
    PostOffer::factory()->accepted()->for($user)->create();
    PostOffer::factory()->create(); // someone else's offer: must not be counted

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertOk();
    $response->assertViewHas('pendingOffersCount', 2);
    $response->assertViewHas('activeExchangesCount', 1);
});

test('the home page counts the user\'s completed swaps and the reviews they received', function () {
    $user = User::factory()->create();
    $completedPost = Post::factory()->for($user)->create(['status' => PostStatus::COMPLETED]);
    $offer = PostOffer::factory()->for($completedPost)->create(['status' => PostOfferStatus::ACCEPTED]);
    PostOffer::factory()->accepted()->for($user)->create(); // still in progress: not completed
    Review::create(['post_offer_id' => $offer->id, 'reviewer_id' => $offer->user_id, 'reviewee_id' => $user->id, 'review' => 4]);

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertViewHas('completedExchangesCount', 1);
    $response->assertViewHas('reviewsCount', 1);
});

test('the home page greets the user according to the time of day', function () {
    $user = User::factory()->create(['username' => 'TostersLV']);

    $this->travelTo(now()->setTime(9, 0));
    $this->actingAs($user)->get(route('home'))->assertSee('Good morning, TostersLV');

    $this->travelTo(now()->setTime(20, 0));
    $this->actingAs($user)->get(route('home'))->assertSee('Good evening, TostersLV');
});

test('the live activity strip shows new exchanges and completed swaps', function () {
    $author = User::factory()->create(['username' => 'Lebron']);
    Post::factory()->create(['offering_skill' => 'Spanish', 'looking_skill' => 'Cooking']);
    Post::factory()->for($author)->create(['offering_skill' => 'Guitar', 'looking_skill' => 'German', 'status' => PostStatus::COMPLETED]);

    $response = $this->actingAs(User::factory()->create())->get(route('home'));

    $response->assertSee('Spanish ⇄ Cooking');
    $response->assertSee('swapped Guitar → German');
    $response->assertSeeText('1 exchange completed this week');
});

test('the header shows how many offers are waiting for the user', function () {
    $user = User::factory()->create();
    PostOffer::factory()->count(3)->for(Post::factory()->for($user))->create();

    $this->actingAs($user)->get(route('posts.requests'))->assertSee('Offers, 3 waiting for you');
});
