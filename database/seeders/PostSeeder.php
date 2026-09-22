<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryIds = Category::pluck('id');

        User::all()->each(function (User $user) use ($categoryIds): void {
            for ($i = 0; $i < 2; $i++) {
                Post::create([
                    'user_id' => $user->id,
                    'category_id' => $categoryIds->random(),
                    'offering_skill' => fake()->words(2, true),
                    'looking_skill' => fake()->words(2, true),
                    'description' => fake()->sentence(),
                ]);
            }
        });
    }
}
