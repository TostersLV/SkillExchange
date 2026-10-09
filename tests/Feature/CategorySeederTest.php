<?php

use App\Models\Category;
use Database\Seeders\CategorySeeder;
use Illuminate\Database\UniqueConstraintViolationException;

test('running the category seeder twice does not duplicate categories', function () {
    $this->seed(CategorySeeder::class);
    $this->seed(CategorySeeder::class);

    expect(Category::count())->toBe(12);
    expect(Category::where('name', 'Programming')->count())->toBe(1);
});

test('the database refuses two categories with the same name', function () {
    Category::factory()->create(['name' => 'Programming']);

    expect(fn () => Category::factory()->create(['name' => 'Programming']))
        ->toThrow(UniqueConstraintViolationException::class);
});
