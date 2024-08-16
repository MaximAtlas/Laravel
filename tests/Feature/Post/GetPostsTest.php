<?php

namespace Tests\Feature\Post;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetPostsTest extends TestCase
{
    use RefreshDatabase;

    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();

        $this->post = Post::factory(5)
            ->for(User::factory())
            ->for(Category::factory())
            ->has(Comment::factory(5)->for(User::factory()), 'comments')
            ->createOne(['status' => PostStatus::Published]);

    }

    public function testGetPost(): void
    {
        $response = $this->get(route('posts.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure(
            ['*' => [
                'id',
                'title',
                'thumbnail',
                'views',
                'created_at',
            ]]);

        $response->assertJsonFragment([
            'id' => $this->post->id,
            'title' => $this->post->title,
            'thumbnail' => $this->post->thumbnail,
            'views' => $this->post->views,
            'created_at' => $this->post->created_at->toIsoString(),
        ]);
    }
}
