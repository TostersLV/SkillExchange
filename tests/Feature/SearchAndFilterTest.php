<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Review;
use App\Models\User;
use App\PostStatus;
use Livewire\Livewire;

test('searching shows only matching posts', function () {
    Post::factory()->create(['offering_skill' => 'Guitar lessons', 'looking_skill' => 'Spanish']);
    Post::factory()->create(['offering_skill' => 'Baking', 'looking_skill' => 'Photography']);

    Livewire::actingAs(User::factory()->create())
        ->test('posts.search-and-filter')
        ->set('search', 'Guitar')
        ->assertSee('Guitar lessons')
        ->assertDontSee('Baking');
});

test('the teach field matches the skill someone is looking for', function () {
    Post::factory()->create(['offering_skill' => 'Yoga basics', 'looking_skill' => 'Spanish']);
    Post::factory()->create(['offering_skill' => 'Baking', 'looking_skill' => 'Photography']);

    Livewire::actingAs(User::factory()->create())
        ->test('posts.search-and-filter')
        ->set('teach', 'Spanish')
        ->assertSee('Yoga basics')
        ->assertDontSee('Baking');
});

test('the learn field does not match the skill someone is looking for', function () {
    Post::factory()->create(['offering_skill' => 'Yoga basics', 'looking_skill' => 'Spanish']);

    Livewire::actingAs(User::factory()->create())
        ->test('posts.search-and-filter')
        ->set('search', 'Spanish')
        ->assertDontSee('Yoga basics');
});

test('swapping the fields exchanges what you learn and what you teach', function () {
    Livewire::actingAs(User::factory()->create())
        ->test('posts.search-and-filter')
        ->set('search', 'Guitar')
        ->set('teach', 'Spanish')
        ->call('swapFields')
        ->assertSet('search', 'Spanish')
        ->assertSet('teach', 'Guitar');
});

test('explore lists only exchanges that are still open to offers', function (PostStatus $status) {
    Post::factory()->create(['offering_skill' => 'Guitar lessons']);
    Post::factory()->create(['offering_skill' => 'Baking', 'status' => $status]);

    Livewire::actingAs(User::factory()->create())
        ->test('posts.search-and-filter')
        ->assertSee('Guitar lessons')
        ->assertDontSee('Baking');
})->with([PostStatus::IN_PROGRESS, PostStatus::COMPLETED, PostStatus::CANCELLED]);

test('posts wanting a skill you offer are marked as a great match and listed first', function () {
    $user = User::factory()->create();
    Post::factory()->for($user)->create(['offering_skill' => 'Spanish']);
    Post::factory()->create(['offering_skill' => 'Baking', 'looking_skill' => 'Photography']);
    Post::factory()->create(['offering_skill' => 'Guitar lessons', 'looking_skill' => 'Spanish 2h', 'created_at' => now()->subWeek()]);

    Livewire::actingAs($user)
        ->test('posts.search-and-filter')
        ->assertSee('Great match for you')
        ->assertSeeInOrder(['Guitar lessons', 'Baking']);
});

test('your own posts are never a match for you', function () {
    $user = User::factory()->create();
    Post::factory()->for($user)->create(['offering_skill' => 'Spanish', 'looking_skill' => 'Spanish lessons']);

    Livewire::actingAs($user)
        ->test('posts.search-and-filter')
        ->assertDontSee('Great match for you');
});

test('sorting by highest rated lists better rated members first', function () {
    $wellRated = User::factory()->create();
    $lowRated = User::factory()->create();
    Review::create(['reviewee_id' => $wellRated->id, 'review' => 5]);
    Review::create(['reviewee_id' => $lowRated->id, 'review' => 2]);
    Post::factory()->for($lowRated)->create(['offering_skill' => 'Baking']);
    Post::factory()->for($wellRated)->create(['offering_skill' => 'Guitar lessons', 'created_at' => now()->subWeek()]);

    Livewire::actingAs(User::factory()->create())
        ->test('posts.search-and-filter')
        ->call('sortBy', 'rated')
        ->assertSeeInOrder(['Guitar lessons', 'Baking']);
});

test('an unknown sort falls back to best match', function () {
    Livewire::actingAs(User::factory()->create())
        ->test('posts.search-and-filter')
        ->call('sortBy', 'cheapest')
        ->assertSet('sort', 'best');
});

test('filtering by category shows only that category', function () {
    $music = Category::factory()->create(['name' => 'Music & Audio']);
    $cooking = Category::factory()->create(['name' => 'Cooking']);
    Post::factory()->for($music)->create(['offering_skill' => 'Guitar lessons', 'looking_skill' => 'Spanish']);
    Post::factory()->for($cooking)->create(['offering_skill' => 'Baking', 'looking_skill' => 'Photography']);

    Livewire::actingAs(User::factory()->create())
        ->test('posts.search-and-filter')
        ->set('categoryId', (string) $music->id)
        ->assertSee('Guitar lessons')
        ->assertDontSee('Baking');
});

test('clearing filters shows all posts again', function () {
    Post::factory()->create(['offering_skill' => 'Guitar lessons', 'looking_skill' => 'Spanish']);
    Post::factory()->create(['offering_skill' => 'Baking', 'looking_skill' => 'Photography']);

    Livewire::actingAs(User::factory()->create())
        ->test('posts.search-and-filter')
        ->set('search', 'Guitar')
        ->call('clearFilters')
        ->assertSee('Guitar lessons')
        ->assertSee('Baking');
});

test('explore shows 15 exchanges per page', function () {
    Post::factory()->count(20)->for(Category::factory())->create();

    Livewire::actingAs(User::factory()->create())
        ->test('posts.search-and-filter')
        ->assertViewHas('posts', fn ($posts) => $posts->count() === 15 && $posts->total() === 20)
        ->assertSee('Page')
        ->call('nextPage')
        ->assertViewHas('posts', fn ($posts) => $posts->count() === 5 && $posts->currentPage() === 2);
});

test('a new search goes back to the first page', function () {
    Post::factory()->count(20)->for(Category::factory())->create();

    Livewire::actingAs(User::factory()->create())
        ->test('posts.search-and-filter')
        ->call('nextPage')
        ->set('search', 'a')
        ->assertViewHas('posts', fn ($posts) => $posts->currentPage() === 1);
});

test('highest rated finds the best rated member even when they would be on a later page', function () {
    $wellRated = User::factory()->create();
    Review::create(['reviewee_id' => $wellRated->id, 'review' => 5]);
    $bestPost = Post::factory()->for($wellRated)->create(['created_at' => now()->subMonth()]);
    Post::factory()->count(16)->for(Category::factory())->create();

    Livewire::actingAs(User::factory()->create())
        ->test('posts.search-and-filter')
        ->call('sortBy', 'rated')
        ->assertViewHas('posts', fn ($posts) => $posts->first()->is($bestPost));
});

test('best match finds a matching post even when it would be on a later page', function () {
    $user = User::factory()->create();
    Post::factory()->for($user)->create(['offering_skill' => 'Chess coaching']);
    $match = Post::factory()->create(['looking_skill' => 'Chess', 'created_at' => now()->subMonth()]);
    Post::factory()->count(16)->for(Category::factory())->create();

    Livewire::actingAs($user)
        ->test('posts.search-and-filter')
        ->assertViewHas('posts', fn ($posts) => $posts->first()->is($match))
        ->assertSee('Great match for you');
});
