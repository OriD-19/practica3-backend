<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('lists all posts of a user without filter', function () {
    $user = User::factory()->create();
    actingAs($user);

    // Crear posts asociados al usuario
    Post::factory()->count(2)->for($user)->create();

    $response = $this->getJson('/api/v1/posts');

    $response->assertStatus(200)
        ->assertJsonCount(2)
        ->assertJsonStructure([
            '*' => ['id', 'title', 'slug', 'excerpt', 'categories', 'user', 'created_at']
        ]);
});

it('lists posts with a search filter', function () {
    $user = User::factory()->create();
    actingAs($user);

    // Crear posts con títulos específicos
    Post::factory()->for($user)->create(['title' => 'Mi nueva publicación']);
    Post::factory()->for($user)->create(['title' => 'Demo']);

    // Filtrar por "Mi nueva publicación"
    $response = $this->getJson('/api/v1/posts?search=nueva');

    $response->assertStatus(200)
        ->assertJsonCount(1) // Solo debe devolver 1 post
        ->assertJsonFragment(['title' => 'Mi nueva publicación']);
});

it('returns authentication error when listing posts without login', function () {
    $response = $this->getJson('/api/v1/posts');

    $response->assertStatus(401);
});
