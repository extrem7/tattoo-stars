<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Post\Comment;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $posts = Post::all(['id']);

        $this->command->getOutput()->progressStart(count($posts));

        $posts->each(function (Post $post) {
            $post->comments()->saveMany(Comment::factory()->count(20)->create([
                'post_id' => $post->id,
                'user_id' => User::whereNotNull('email_verified_at')->inRandomOrder()->first()->id
            ]));

            $this->command->getOutput()->progressAdvance();
        });

        $this->command->getOutput()->progressFinish();
    }
}
