<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereNotNull('email_verified_at')->get(['id']);
        $this->command->getOutput()->progressStart(count($users));

        $users->each(function (User $user) {
            Post::factory()->count(15)->create(['user_id' => $user->id])->each(function (Post $post) {
                for ($i = 0; $i < random_int(1, 6); $i++) {
                    $post->addMediaFromUrl('https://picsum.photos/500/500')->toMediaCollection('images');
                }
                if (random_int(1, 3) === 1) {
                    $videos = [
                        'https://media.istockphoto.com/videos/fly-over-the-colourful-trees-in-a-wood-tops-of-a-fir-trees-video-id1225189918',
                        'http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
                        'https://media.istockphoto.com/videos/luxury-sedan-driving-in-a-green-pine-forest-video-id1184725533',
                        'http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4'
                    ];
                    $post->addMediaFromUrl($videos[random_int(0, count($videos) - 1)])->toMediaCollection('video');
                }
            });

            $this->command->getOutput()->progressAdvance();
        });

        $this->command->getOutput()->progressFinish();
    }
}
