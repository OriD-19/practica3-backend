<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Create a post successfully', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $post = Post::factory()
        ->for($user)
        ->has(Category::factory()->count(rand(1, 5)))
        ->create();

    $response = $this->postJson('/api/v1/posts', [
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
});

test('Validate post details', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $post = Post::factory()
        ->for($user)
        ->has(Category::factory()->count(3))
        ->create();

    // remove both the title and the content (not the only validations)
    $post->title = "";
    $post->content = "";

    $response = $this->postJson('/api/v1/posts', [
        'title' => $post->title,
        'excerpt' => $post->excerpt,
        'content' => $post->content,
        'categories' => $post->categories->pluck('id')->toArray(),
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'content']); //creo que es asi askdfdkkf
});

test('Validate same post slug', function () {

    $user = User::factory()->create();
    $this->actingAs($user);

    $existingPost = Post::factory()
        ->for($user)
        ->has(Category::factory()->count(1))
        ->create();

    $post = Post::factory()
        ->for($user)
        ->has(Category::factory()->count(rand(1, 5)))
        ->make(['title' => $existingPost->title]);

    $response = $this->postJson('/api/v1/posts', [
        'title' => $post->title,
        'excerpt' => $post->excerpt,
        'content' => $post->content,
        'categories' => $post->categories->pluck('id')->toArray(),
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['id', 'title', 'slug'])
        ->assertJsonFragment(['title' => $post->title]);
});

test('Authentication error when creating post', function () {
    $user = User::factory()->create(); // creating a user, but not authenticating it

    $post = Post::factory()
    ->for($user)
    ->has(Category::factory()->count(3))
    ->make(); // make does not persist the post into the database

    $response = $this->postJson('/api/v1/posts', [
        'title' => $post->title,
        'excerpt' => $post->excerpt,
        'content' => $post->content,
        'categories' => Category::factory()->count(1)->create()->pluck('id')->toArray(),
    ]);

    $response->assertStatus(401);
});
