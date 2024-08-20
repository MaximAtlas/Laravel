<?php

namespace Tests\Feature\Post;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UpdatePostTest extends TestCase
{
    private int $postId;

    private array $dataPut;
    private array $dataPatch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    public function testUpdatePost(): void
    {

        $category = (Category::factory()->createOne());
        $this->dataPut = [
            'title' => fake()->sentence,
            'content' => fake()->text,
            'state' => 'published',
            'category_name' => $category->name,
        ];

        $this->dataPatch = ['state' => 'private'];

        $this->postId = Post::query()->inRandomOrder()->first()->id;

        $this->testPutPost();

        $this->testPatchPost();


    }

    private function testPutPost(): void
    {
        $response = $this->json('Put', route('posts.put', ['post' => $this->postId]), ($this->dataPut));

        $response->assertOk();
        $response->assertJsonStructure(['message']);


        $this->testUpdatedPutPost();
    }

    private function testUpdatedPutPost(): void
    {
        $this->assertDatabaseHas('posts', [
            'id' => $this->postId,
            'title' => $this->dataPut['title'],
            'body' => $this->dataPut['content'],
            'status' => $this->dataPut['state'],
            'category_id' => Category::query()->where('name', $this->dataPut['category_name'])->first()->id,
        ]);

    }

    private function testPatchPost(): void
    {
        $response = $this->json('patch', route('posts.patch', ['post' => $this->postId]), ($this->dataPatch));

        $response->assertOk();
        $response->assertJsonStructure(['message']);


        $this->testUpdatedPatchPost();
    }

    private function testUpdatedPatchPost(): void
    {
        $this->assertDatabaseHas('posts', [
            'id' => $this->postId,
            'status' => $this->dataPatch['state'],
        ]);
    }

  public function test_patch_post_failed_validation(): void
    {

        $this->postId = Post::query()->inRandomOrder()->first()->id;

        $this->dataPatch = [
            'titwle' => fake()->word,
        ];

        $response = $this->json('Patch', route('posts.patch', ['post' => $this->postId]), ($this->dataPatch));

        //dd($response->status(), $response->headers->all(), $response->getContent());

        $response->assertStatus(204);

    }

    public function test_put_post_failed_validation(): void
    {

        $this->postId = Post::query()->inRandomOrder()->first()->id;

        $this->dataPut = [
            'titwle' => fake()->word,
        ];

        $response = $this->json('Put', route('posts.put', ['post' => $this->postId]), ($this->dataPut));


        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'state', 'category_name']);
    }

}
