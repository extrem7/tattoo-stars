<?php

namespace Database\Seeders;

use App\Models\User\AccountType;
use Illuminate\Database\Seeder;

class AccountTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['user', 'shop', 'studio', 'master'];
        foreach ($types as $name) {
            AccountType::create(compact('name'));
        }
    }
}
