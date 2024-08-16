<?php

namespace Tests\Feature\Post;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class DeletePostTest extends TestCase
{
    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn();

        $this->post = Post::factory()
            ->for(User::factory())
            ->for(Category::factory())
            ->createOne();
    }

    public function test_delete_product(): void
    {
        $response = $this->delete(route('posts.destroy', ['post' => $this->post->id]));

        $response->assertOk();
        $this->assertDatabaseMissing(Post::class, [
            'id' => $this->post->id,
        ]);
    }
}
