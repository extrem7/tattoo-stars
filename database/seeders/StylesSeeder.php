<?php

namespace Database\Seeders;

use App\Models\User\Style;
use Illuminate\Database\Seeder;

class StylesSeeder extends Seeder
{
    public function run(): void
    {
        $styles = ['Graphics', 'Style 2', 'Style 3', 'Style 4', 'Style 5', 'Style 6'];
        foreach ($styles as $name) {
            Style::create(compact('name'));
        }
    }
}
