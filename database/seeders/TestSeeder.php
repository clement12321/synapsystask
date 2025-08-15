<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Content;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the test user
        $user = User::updateOrCreate(
            ['username' => 'test'],
            ['password' => Hash::make('password')]
        );

        // Create user details
        UserDetail::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => 'Mr. Test',
                'country' => 'Malaysia',
            ]
        );

        // Seed 5 random content entries
        for ($i = 1; $i <= 5; $i++) {
            Content::create([
                'user_id' => $user->id,
                'title' => 'Test Content ' . $i,
                'body' => 'Seeded Test for content #' . $i,
                'is_featured' => rand(0, 1),
                'image_path' => 'content_images/sunset.png'
            ]);
        }
    }
}
