<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->createOne([
            'email' => 'admin@somecode.pro',
            'role' => UserRole::Admin,
        ]);

        Category::factory(3)
            ->has(
                Post::factory(5)
                    ->for(
                        User::factory()
                            ->create(['role' => UserRole::Moderator])
                    )
                    ->has(
                        Comment::factory(5)
                            ->for(
                                User::factory()
                                    ->create(['role' => UserRole::User])
                            )
                    )
            )
            ->create();
    }
}
