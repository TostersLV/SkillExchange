<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'Programming',
            'Design',
            'Writing & Translation',
            'Marketing',
            'Music & Audio',
            'Languages',
            'Photography',
            'Video & Animation',
            'Business',
            'Data & Analytics',
            'Crafts & DIY',
            'Fitness & Wellness',
        ];

        // firstOrCreate makes the seeder safe to run again: existing categories are left as they are
        foreach ($names as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
