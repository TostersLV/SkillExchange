<?php

use App\Models\Category;
use App\Models\Message;
use App\Models\Post;
use App\Models\PostOffer;
use App\Models\User;
use App\PostOfferStatus;
use App\PostStatus;
use Illuminate\Database\QueryException;
use Livewire\Livewire;

test('the owner can delete their post', function () {
    $post = Post::factory()->create();

    $this->actingAs($post->user)
        ->delete(route('posts.destroy', $post))
        ->assertRedirect(route('home'));

    $this->assertModelMissing($post);
});

test('other users cannot delete a post', function () {
    $post = Post::factory()->create();
    $stranger = User::factory()->create();

    $this->actingAs($stranger)
        ->delete(route('posts.destroy', $post))
        ->assertForbidden();

    $this->assertModelExists($post);
});

test('the owner can open the edit page', function () {
    $post = Post::factory()->create();

    $this->actingAs($post->user)
        ->get(route('posts.edit', $post))
        ->assertOk();
});

test('other users cannot open the edit page', function () {
    $post = Post::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('posts.edit', $post))
        ->assertForbidden();
});

test('the owner can update their post', function () {
    $post = Post::factory()->create();
    $newCategory = Category::factory()->create();

    $this->actingAs($post->user)
        ->put(route('posts.update', $post), [
            'offering_skill' => 'Guitar lessons',
            'looking_skill' => 'Spanish',
            'category_id' => $newCategory->id,
            'description' => 'Weekends work best for me.',
        ])
        ->assertRedirect(route('posts.show', $post));

    $post->refresh();
    expect($post->offering_skill)->toBe('Guitar lessons');
    expect($post->looking_skill)->toBe('Spanish');
    expect($post->category_id)->toBe($newCategory->id);
});

test('other users cannot update a post', function () {
    $post = Post::factory()->create(['offering_skill' => 'Web design']);

    $this->actingAs(User::factory()->create())
        ->put(route('posts.update', $post), [
            'offering_skill' => 'Hacked skill',
            'looking_skill' => 'Spanish',
            'category_id' => $post->category_id,
        ])
        ->assertForbidden();

    expect($post->fresh()->offering_skill)->toBe('Web design');
});

test('updating a post validates the input', function () {
    $post = Post::factory()->create();

    $this->actingAs($post->user)
        ->put(route('posts.update', $post), [
            'offering_skill' => 'ab',     // too short (min 4)
            'looking_skill' => '',        // required
            'category_id' => 999,         // category doesn't exist
        ])
        ->assertSessionHasErrors(['offering_skill', 'looking_skill', 'category_id']);
});

test('the owner cannot delete a post once an offer is accepted', function (PostStatus $status) {
    $post = Post::factory()->create(['status' => $status]);

    $this->actingAs($post->user)
        ->delete(route('posts.destroy', $post))
        ->assertForbidden();

    $this->assertModelExists($post);
})->with([PostStatus::IN_PROGRESS, PostStatus::COMPLETED, PostStatus::CANCELLED]);

test('the owner cannot update a post once an offer is accepted', function (PostStatus $status) {
    $post = Post::factory()->create(['status' => $status, 'offering_skill' => 'Web design']);

    $this->actingAs($post->user)
        ->put(route('posts.update', $post), [
            'offering_skill' => 'Guitar lessons',
            'looking_skill' => 'Spanish',
            'category_id' => $post->category_id,
        ])
        ->assertForbidden();

    expect($post->fresh()->offering_skill)->toBe('Web design');
})->with([PostStatus::IN_PROGRESS, PostStatus::COMPLETED, PostStatus::CANCELLED]);

test('the owner cannot open the edit page once an offer is accepted', function () {
    $post = Post::factory()->create(['status' => PostStatus::IN_PROGRESS]);

    $this->actingAs($post->user)
        ->get(route('posts.edit', $post))
        ->assertForbidden();
});

test('the owner sees edit and delete actions only while the post is available', function () {
    $available = Post::factory()->create();
    $inProgress = Post::factory()->for($available->user)->create(['status' => PostStatus::IN_PROGRESS]);

    $this->actingAs($available->user)
        ->get(route('posts.show', $available))
        ->assertSee(route('posts.edit', $available))
        ->assertSee('action="'.route('posts.destroy', $available).'"', false);

    $this->actingAs($inProgress->user)
        ->get(route('posts.show', $inProgress))
        ->assertOk()
        ->assertDontSee(route('posts.edit', $inProgress))
        ->assertDontSee('action="'.route('posts.destroy', $inProgress).'"', false);
});

test('the owner cannot update a post once someone has sent an offer', function () {
    $post = PostOffer::factory()->create()->post;
    $originalSkill = $post->offering_skill;

    $this->actingAs($post->user)
        ->put(route('posts.update', $post), [
            'offering_skill' => 'Guitar lessons',
            'looking_skill' => 'Spanish',
            'category_id' => $post->category_id,
        ])
        ->assertForbidden();

    expect($post->fresh()->offering_skill)->toBe($originalSkill);
});

test('the owner does not see the edit action once someone has sent an offer', function () {
    $post = PostOffer::factory()->create()->post;

    $this->actingAs($post->user)
        ->get(route('posts.show', $post))
        ->assertOk()
        ->assertDontSee(route('posts.edit', $post));
});

test('guests cannot edit posts', function () {
    $post = Post::factory()->create();

    $this->get(route('posts.edit', $post))->assertRedirect(route('login'));
});

test('other members see a propose swap button on an available post', function () {
    $post = Post::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('posts.show', $post))
        ->assertOk()
        ->assertSee($post->offering_skill)
        ->assertSee('Propose swap');
});

test('the owner does not get a propose swap button on their own post', function () {
    $post = Post::factory()->create();

    $this->actingAs($post->user)
        ->get(route('posts.show', $post))
        ->assertOk()
        ->assertDontSee('Propose swap');
});

test('members can open the post an exchange page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('posts.create'))
        ->assertOk()
        ->assertSee('You offer')
        ->assertSee('You want in return');
});

test('the owner cannot delete a post that ever received an offer', function (PostOfferStatus $status) {
    $offer = PostOffer::factory()->create(['status' => $status]);

    $this->actingAs($offer->post->user)
        ->delete(route('posts.destroy', $offer->post))
        ->assertForbidden();

    $this->assertModelExists($offer->post);
})->with([PostOfferStatus::PENDING, PostOfferStatus::REJECTED, PostOfferStatus::WITHDRAWN, PostOfferStatus::CANCELLED]);

test('a post with offers offers close instead of delete', function () {
    $post = PostOffer::factory()->create()->post;

    $this->actingAs($post->user)
        ->get(route('posts.show', $post))
        ->assertSee('Close post')
        ->assertDontSee('action="'.route('posts.destroy', $post).'"', false);
});

test('closing a post declines waiting offers and keeps all history', function () {
    $cancelledExchange = PostOffer::factory()->create(['status' => PostOfferStatus::CANCELLED]);
    $post = $cancelledExchange->post;
    $message = Message::factory()->for($cancelledExchange, 'postOffer')->create();
    $waiting = PostOffer::factory()->for($post)->create();

    $this->actingAs($post->user)
        ->patch(route('posts.close', $post))
        ->assertRedirect(route('posts.show', $post));

    expect($post->fresh()->status)->toBe(PostStatus::CANCELLED);
    expect($waiting->fresh()->status)->toBe(PostOfferStatus::REJECTED);
    expect($cancelledExchange->fresh()->status)->toBe(PostOfferStatus::CANCELLED);
    $this->assertModelExists($message);
});

test('only the owner can close a post, and only one that has offers', function () {
    $postWithOffer = PostOffer::factory()->create()->post;
    $postWithoutOffers = Post::factory()->create();

    $this->actingAs(User::factory()->create())
        ->patch(route('posts.close', $postWithOffer))
        ->assertForbidden();

    $this->actingAs($postWithoutOffers->user)
        ->patch(route('posts.close', $postWithoutOffers))
        ->assertForbidden();

    expect($postWithOffer->fresh()->status)->toBe(PostStatus::AVAILABLE);
});

test('a closed post disappears from explore', function () {
    $closed = Post::factory()->create(['status' => PostStatus::CANCELLED, 'offering_skill' => 'Closed chess']);
    $open = Post::factory()->create(['offering_skill' => 'Open piano']);

    $this->actingAs(User::factory()->create());

    Livewire::test('posts.search-and-filter')
        ->assertSee($open->offering_skill)
        ->assertDontSee($closed->offering_skill);
});

test('the sender is told when the author closed the post', function () {
    $offer = PostOffer::factory()->create();

    $this->actingAs($offer->post->user)->patch(route('posts.close', $offer->post));

    $this->actingAs($offer->user)
        ->get(route('posts.requests'))
        ->assertSee($offer->post->user->username.' closed this post.');
});

test('the database refuses to delete a post that has offers', function () {
    $offer = PostOffer::factory()->create();

    expect(fn () => $offer->post->delete())->toThrow(QueryException::class);

    $this->assertModelExists($offer);
});
