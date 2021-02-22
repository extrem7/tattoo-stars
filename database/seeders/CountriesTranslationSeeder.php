<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use Stichoza\GoogleTranslate\GoogleTranslate;

class CountriesTranslationSeeder extends Seeder
{
    public function run(): void
    {
        app()->setLocale('en');

        $gt = new GoogleTranslate('ru');
        $gt->setSource('en');

        $countries = Country::all();
        $names = implode("\n", $countries->pluck('name')->toArray());
        $translated = explode("\n", $gt->translate($names));

        \DB::transaction(
            fn() => $countries->map(fn(Country $c, $key) => $c->update(['name_ru' => $translated[$key]]))
        );
    }
}
