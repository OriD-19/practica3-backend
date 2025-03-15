<?php

use App\Http\Controllers\PostController;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Create a duplicate slug just works', function () {
    PostController::createSlug('Nuevo titulo'); // create a slug
    $repeatedSlug = PostController::createSlug('Nuevo titulo'); // create a repeated slug

    expect($repeatedSlug)->toBeString('nuevo-titulo-1');
});

test('Create a single slug works fine', function() {
    $slug = PostController::createSlug('Nuevo Titulo');

    expect($slug)->toBeString('nuevo-titulo');
});