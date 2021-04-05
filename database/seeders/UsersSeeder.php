<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        if (User::find(1) === null) {
            $user = new User([
                'name' => env('INITIAL_USER_NAME'),
                'email' => env('INITIAL_USER_EMAIL'),
                'password' => env('INITIAL_USER_PASSWORDHASH'),
            ]);
            $user->save();
            $user->assignRole('admin');
        }

        $this->command->getOutput()->progressStart(10);

        User::factory()->has(User\Information::factory())->count(10)->create()->each(function (User $user) {
            $user->styles()->sync(User\Style::inRandomOrder()->limit(2)->pluck('id'));
            $user->addMediaFromUrl('https://i.pravatar.cc/225')->toMediaCollection('avatar');

            $this->command->getOutput()->progressAdvance();
        });

        $this->command->getOutput()->progressFinish();
    }
}
