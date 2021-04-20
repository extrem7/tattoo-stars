<?php

namespace Database\Factories\Post;

use App\Models\Post\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'text' => $this->faker->sentence
        ];
    }
}
