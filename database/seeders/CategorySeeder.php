<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $insert = [
            ['name' => 'Programming'],
            ['name' => 'Design'],
            ['name' => 'Writing & Translation'],
            ['name' => 'Marketing'],
            ['name' => 'Music & Audio'],
            ['name' => 'Languages'],
            ['name' => 'Photography'],
            ['name' => 'Video & Animation'],
            ['name' => 'Business'],
            ['name' => 'Data & Analytics'],
            ['name' => 'Crafts & DIY'],
            ['name' => 'Fitness & Wellness'],
        ];

        \DB::table('categories')->insert($insert);
    }
}
