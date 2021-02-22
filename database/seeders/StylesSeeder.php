<?php

namespace Database\Seeders;

use App\Models\User\Style;
use Illuminate\Database\Seeder;

class StylesSeeder extends Seeder
{
    public function run(): void
    {
        $styles = [
            'Black&grey', 'Abstraction', 'Watercolor', 'Baroque', 'Biomechanics', 'Blackwork', 'Geometric', 'Engraving',
            'Graphics', 'Linework', 'Dotwork', 'Cyberpunk', 'Lettering', 'Maori', 'Mayan', 'Miniature', 'Minimalism',
            'Neo Traditional', 'New School', 'Old School', 'Ornamental', 'Pin-Up', 'Polynesian', 'Portrait', 'Realism',
            'Scetch', 'Surrealism', 'Traditional', 'Tribal', 'Trash Polka', 'Ultraviolet', 'Fantasy', 'Hand-Poked',
            'Horror', 'Chicano', 'Japanese', '3D'
        ];
        foreach ($styles as $name) {
            Style::create([
                'name' => \Str::slug($name)
            ]);
        }
    }
}
