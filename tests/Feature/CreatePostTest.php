<?php

use App\Models\Post;
use App\Models\User;
use Database\Factories\PostFactory;

test('Create a post successfully', function () {

    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    $response = $this->post('/api/v1/posts', [
        'title' => $post->title,
        'excerpt' => $post->excerpt,
        'content' => $post->content,
        'categories' => $post->categories,
    ]);

    $response->dump();
    $response->assertStatus(200);
});
