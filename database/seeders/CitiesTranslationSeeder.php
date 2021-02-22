<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Stichoza\GoogleTranslate\GoogleTranslate;

class CitiesTranslationSeeder extends Seeder
{
    public function run(): void
    {
        app()->setLocale('en');

        $gt = new GoogleTranslate('ru');
        $gt->setSource('en');

        $cities = City::whereNull('name_ru')->get(['id', 'name']);
        $this->command->getOutput()->progressStart(count($cities) / 300);
        $cities->chunk(300)->each(function (Collection $chunk) use ($gt) {
            $names = implode("\n", $chunk->pluck('name')->toArray());
            $translated = explode("\n", $gt->translate($names));

            \DB::transaction(
                fn() => $chunk->values()->each(fn(City $c, $key) => $c->update(['name_ru' => $translated[$key]]))
            );

            $this->command->getOutput()->progressAdvance();
        });

        $this->command->getOutput()->progressFinish();
    }
}
