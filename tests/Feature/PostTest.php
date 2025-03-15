<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function create_new_post()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $post = Post::factory()->for($user)->create();

        $response = $this->postJson('/api/posts', [
            'title' => $post->title,
            'excerpt' => $post->excerpt,
            'content' => $post->content,
            'categories' => $post->categories->pluck('id')->toArray(),
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'title',
                'slug',
                'excerpt',
                'content',
                'categories',
                'user',
                'created_at',
                'updated_at'
            ])
            ->assertJsonFragment(['title' => $post->title, 'excerpt' => $post->excerpt]);
    }


    public function validate_post_details()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/posts', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content']); //creo que es asi askdfdkkf
    }

    public function validate_same_post_slug()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $existingPost = Post::factory()->create();
        $post = Post::factory()->for($user)->make(['title' => $existingPost->title]);

        $response = $this->postJson('/api/posts', [
            'title' => $post->title,
            'excerpt' => $post->excerpt,
            'content' => $post->content,
            'categories' => Category::factory()->count(1)->create()->pluck('id')->toArray(),
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'title', 'slug'])
            ->assertJsonFragment(['title' => $post->title]);
    }

    public function auth_error_creating_post()
    {
        $post = Post::factory()->make();

        $response = $this->postJson('/api/posts', [
            'title' => $post->title,
            'excerpt' => $post->excerpt,
            'content' => $post->content,
            'categories' => Category::factory()->count(1)->create()->pluck('id')->toArray(),
        ]);

        $response->assertStatus(401);
    }
}
