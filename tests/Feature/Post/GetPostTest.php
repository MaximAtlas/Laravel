<?php

namespace Tests\Feature\Post;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetPostTest extends TestCase
{

    private Post $post;

    private Post $draftPost;

    protected function setUp(): void
    {
        parent::setUp();

        $this->post = Post::factory()
            ->for(User::factory())
            ->for(Category::factory())
            ->has(Comment::factory(5)->for(User::factory()), 'comments')
            ->createOne(['status' => PostStatus::Published]);

        $this->draftPost = Post::factory()
            ->for(User::factory())
            ->for(Category::factory())
            ->createOne(['status' => PostStatus::Draft]);
    }

    public function testDraftProduct(): void
    {
        $response = $this->get(route('posts.show', ['post' => $this->draftPost->id]));

        $response->assertNotFound();
        $response->assertJsonStructure(['error']);
        $response->assertJson(['error' => __('messages.PostNotFound')]);
    }

    public function testGetPost(): void
    {
        $response = $this->get(route('posts.show', ['post' => $this->post->id]));

        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'title',
                'body',
                'views',
                'authorName',
                'createdAt',
                'categoryName',
                'comments' => [
                    '*' => ['username', 'text'],
                ],

            ]);

        $response->assertJson([
            'title' => $this->post->title,
            'body' => $this->post->body,
            'views' => $this->post->views,
            'authorName' => $this->post->user->name,
            'createdAt' => $this->post->created_at->toIsoString(),   //Приведение к строке
            'categoryName' => $this->post->category->name,
            'comments' => $this->getComments(),
        ]);
    }

    public function testPostNotFound(): void
    {
        $response = $this->get(route('posts.show', ['post' => 0]));

        $response->assertStatus(404);
        $response->assertJsonStructure(['error']);
        $response->assertJson(['error' => __('messages.ModelNotFound')]);

    }

    private function getComments(): array
    {
        return $this->post->comments->map(fn (Comment $comment) => [
            'username' => $comment->user->name,
            'text' => $comment->text,
        ])->toArray();
    }
}
