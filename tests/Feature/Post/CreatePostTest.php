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

        $response = $this->post(route('posts.store'), $this->data);

        $message = ($response->json())['message'];

        $response->assertCreated();
        $response->assertJsonStructure(['message']);

        preg_match('/id:(\d+)/', $message, $matches);

        if (isset($matches)) {
            $this->postId = (int) $matches[1];
        } else {
            dd('$id не найден');
        }

        $this->testGetCreatedPost();
    }

    private function testGetCreatedPost(): void
    {

        $getResponse = $this->get(route('posts.show', ['post' => $this->postId]));

        $getResponse->assertStatus(200);

        $getResponse->assertJson([
            'title' => $this->data['title'],
            'body' => $this->data['content'],
            'categoryName' => $this->data['category_name'],
        ]);
    }

    public function test_create_product_failed_validation(): void
    {

        $this->data = [
            'title' => fake()->word,
        ];

        $response = $this->post(route('posts.store'), $this->data);

        //dd($response->status(), $response->headers->all(), $response->getContent());

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'state', 'category name']);
    }
}
