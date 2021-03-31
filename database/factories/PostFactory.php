<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $tags = [];
        foreach (explode(' ', $this->faker->words) as $word) {
            $tags[] = "#$word";
        }
        return [
            'description' => $this->faker->sentence,
            'tags' => implode('', $tags)
        ];
    }
}
