<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SubscriptionsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all(['id']);
        $this->command->getOutput()->progressStart(count($users));

        $users->each(function (User $u) {
            $u->subscriptions()->sync(User::limit(random_int(20, 50))->latest()->pluck('id'));
            $this->command->getOutput()->progressAdvance();
        });

        $this->command->getOutput()->progressFinish();
    }
}
