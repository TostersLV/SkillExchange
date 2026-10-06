<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\PostStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            // The two lists share no words, so a random post can never "match itself" in search tests.
            'offering_skill' => fake()->randomElement(['Web design', 'Math tutoring', 'Chess coaching', 'Piano basics']),
            'looking_skill' => fake()->randomElement(['Photography', 'Car repair', 'German classes', 'Gardening']),
            'description' => $this->faker->sentence,
            'status' => PostStatus::AVAILABLE,
        ];
    }
}
