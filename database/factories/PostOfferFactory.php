<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\PostOffer;
use App\Models\User;
use App\PostOfferStatus;
use App\PostStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PostOffer>
 */
class PostOfferFactory extends Factory
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
            'post_id' => Post::factory(),
            'message' => fake()->sentence(),
            'status' => PostOfferStatus::PENDING,
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn () => [
            'status' => PostOfferStatus::ACCEPTED,
            'post_id' => Post::factory()->state(['status' => PostStatus::IN_PROGRESS]),
        ]);
    }
}
