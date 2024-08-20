<?php

namespace Tests\Feature\Post;

use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;


class CreatePostTest extends TestCase
{
    private int $postId;

    private array $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    public function testCreatePost(): void
    {

        $category = (Category::factory()->createOne());
        $this->data = [
            'title' => fake()->sentence,
            'content' => fake()->text,
            'image' => UploadedFile::fake()
                ->image(fake()->word.'.png', 100, 100)->size(100),
            'state' => 'published',
            'category_name' => $category->name,
        ];

        $response =  $this->json('Post', route('posts.store'), ($this->data));

        $response->assertCreated();
        $response->assertJsonStructure(['message']);


        $message = ($response->json())['message'];

        $this->postId = $this->takePostId($message);

        $this->testGetCreatedPost();
    }

    private function testGetCreatedPost(): void
    {
        $this->assertDatabaseHas('posts', [
                'id' => $this->postId,
                'title' => $this->data['title'],
                'body' => $this->data['content'],
                'status' => $this->data['state'],
                'category_id' => Category::query()->where('name', $this->data['category_name'])->first()->id,
        ]);

    }

    public function test_create_product_failed_validation(): void
    {

        $this->data = [
            'titwle' => fake()->word,
        ];

        $response = $this->json('Post', route('posts.store'), ($this->data));


        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'state', 'category_name']);
    }
}
