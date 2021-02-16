<?php

namespace Database\Seeders;

use App\Models\User\Gender;
use Illuminate\Database\Seeder;

class GendersSeeder extends Seeder
{
    public function run(): void
    {
        $genders = ['male', 'female'];
        foreach ($genders as $name) {
            Gender::create(compact('name'));
        }
    }
}
