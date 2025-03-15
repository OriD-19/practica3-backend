<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('List all posts from the authenticated user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Post::factory()->count(2)->for($user)->create();

    $response = $this->getJson('/api/v1/posts');

    $response->assertStatus(200)
        ->assertJsonCount(2)
        ->assertJsonStructure( // each item inside the list has the following structure
            [
                '*' => ['id', 'title', 'slug', 'excerpt', 'categories', 'user', 'created_at'],
            ]
        );
});

it('List all posts from user with filters', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // specific titles, for triggering the search functionality
    Post::factory()->for($user)->create(['title' => 'Mi nueva publicación']);
    Post::factory()->for($user)->create(['title' => 'Demo']);

    // search for partial match
    $response = $this->getJson('/api/v1/posts?search=nueva');

    $response->assertStatus(200)
        ->assertJsonCount(1)
        ->assertJsonFragment(['title' => 'Mi nueva publicación']);
});

it('Return an authentication error for unauthorized users', function () {
    $response = $this->getJson('/api/v1/posts');

    $response->assertStatus(401);
});
