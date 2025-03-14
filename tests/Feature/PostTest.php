<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function create_new_post() {
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'title' => 'New post',
            'excerpt' => 'loquesea',
            'content' => 'Este post trata sobre muchos temas interesantes',
            'categories' => [1, 3]
        ];

        $response = $this->postJson('/api/posts', $data);

        $response->assertStatus(201)
                 ->assertJsonStructure(['id', 'title', 'slug', 'excerpt', 'content', 'categories']);
    }

    public function validate_post_details()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'title' => '', 
            'excerpt' => 'loquesea',
            'content' => '' 
        ];

        $response = $this->postJson('/api/posts', $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['title', 'content']); //creo que es asi askdfdkkf
    }

    public function validate_same_post_slug()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Post::factory()->create(['title' => 'New post']);

        $data = [
            'title' => 'New post', 
            'excerpt' => 'tambiencualquiercosa',
            'content' => 'info super entretenida',
            'categories' => [2]
        ];

        $response = $this->postJson('/api/posts', $data);

        $response->assertStatus(201)
                 ->assertJsonStructure(['id', 'title', 'slug']);
    }

    public function auth_error_creating_post()
    {
        $data = [
            'title' => 'Post sin auth',
            'excerpt' => 'extracto de un buen post',
            'content' => 'texto con mucho conocimiento',
            'categories' => [1]
        ];

        $response = $this->postJson('/api/posts', $data);

        $response->assertStatus(401);
    }
}
