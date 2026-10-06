<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\PostOffer;
use App\Models\User;
use App\PostStatus;

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
