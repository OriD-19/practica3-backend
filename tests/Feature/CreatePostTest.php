<?php

test('Create a post successfully', function () {
    $response = $this->post('/api/v1/posts', );

    $response->assertStatus(200);
});
