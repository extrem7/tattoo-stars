<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdvertisingSeeder extends Seeder
{
    protected array $accountTypes = ['users', 'other', null];

    public function run(): void
    {
        \DB::transaction(function () {
            $posts = Post::inRandomOrder()->limit(15)->get();
            $posts->each(function (Post $post) {
                $promotion = $post->promotions()->create([
                    'post_id' => $post->id,
                    'city_id' => random_int(0, 1) ? City::where('country_id', '=', 'UA')->inRandomOrder()->first()->id : null,
                    'account_type' => $this->accountTypes[random_int(0, 2)],
                    'budget' => random_int(3, 8) * 1000
                ]);
                $promotion->verified = random_int(0, 1) ? true : null;
                $promotion->on_pause = !random_int(0, 4) ? true : null;
                $promotion->save();
            });
        });

        \DB::transaction(function () {
            for ($i = 0; $i < 15; $i++) {
                $banner = User::inRandomOrder()->first()->banners()->create([
                    'city_id' => random_int(0, 1) ? City::where('country_id', '=', 'UA')->inRandomOrder()->first()->id : null,
                    'budget' => random_int(3, 8) * 1000,
                    'redirect_to_site' => random_int(0, 1) ? true : null
                ]);
                $banner->addMediaFromUrl('https://i.pravatar.cc/225')->toMediaCollection('image');
                $banner->verified = random_int(0, 1) ? true : null;
                $banner->on_pause = !random_int(0, 4) ? true : null;
                $banner->save();
            }
        });

        \DB::transaction(function () {
            User::whereDoesntHave('tops')->inRandomOrder()->limit(15)->get()->each(function (User $user) {
                $days = random_int(3, 10);
                $top = $user->tops()->create(['days' => $days]);
                $top->start_at = now();
                $top->end_at = now()->addDays($days);
                $top->save();
            });
        });
    }
}
