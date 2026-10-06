<?php

use App\Models\Category;
use App\Models\Post;
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

test('only available hides exchanges that are no longer open', function () {
    Post::factory()->create(['offering_skill' => 'Guitar lessons']);
    Post::factory()->create(['offering_skill' => 'Baking', 'status' => PostStatus::COMPLETED]);

    Livewire::actingAs(User::factory()->create())
        ->test('posts.search-and-filter')
        ->assertSee('Baking')
        ->set('onlyAvailable', true)
        ->assertSee('Guitar lessons')
        ->assertDontSee('Baking');
});

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
    $wellRated = User::factory()->create(['reputation' => 4.8]);
    $lowRated = User::factory()->create(['reputation' => 2.1]);
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
