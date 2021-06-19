<?php

namespace Database\Seeders;

use App\Models\ContestWork;
use App\Models\Story;
use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Api\Services\ContestService;

class ContestSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::inRandomOrder()->whereHas('posts')->limit(15)->get();
        $stories = collect();

        $users->each(function (User $user) use ($stories) {
            $story = $user->posts()->latest()->first()->stories()->create();
            $story->created_at = today()->addDays(-1);
            $story->save();
            $stories->push($story);
        });

        $stories->each(function (Story $story) use ($users) {
            $story->likes()->sync($users->slice(0, random_int(1, $users->count())));
        });

        $service = app(ContestService::class);
        $service->startDailyContest();

        ContestWork::daily()->inRandomOrder()->each(function (ContestWork $work) use ($users) {
            $work->votes()->sync($users->slice(0, random_int(1, $users->count())));
            $work->date = today()->addDays(-1);
            $work->save();
        });

        $service->calculateResults();
    }
}
