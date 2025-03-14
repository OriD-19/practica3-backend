<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $postTitle = fake()->sentence();
        
        return [
            //
            'title' => $postTitle,
            'slug' => Str::of($postTitle)->slug('-'),
            'excerpt' => fake()->sentence(),
            'content' => fake()->paragraphs(5, true),
        ];
    }
}
