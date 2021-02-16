<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;

class CitiesSeeder extends Seeder
{
    public function run(): void
    {
        $cities = [];

        $file_handle = fopen(resource_path('db/worldcities.csv'), 'rb');
        while (!feof($file_handle)) {
            $cities[] = fgetcsv($file_handle, 0, ',');
        }
        fclose($file_handle);

        $columns = array_shift($cities);
        array_pop($cities);

        $cities = array_map(function ($city) use ($columns) {
            $mapped = [];

            foreach ($city as $key => $value) {
                $mapped[$columns[$key]] = $value;
            }

            return $mapped;
        }, $cities);

        /*collect($cities)->unique('iso2')->map(fn($c) => ['id' => $c['iso2'], 'name' => $c['country']])->each(function ($c) {
            Country::insert([
                'id' => $c['id'],
                'name' => $c['name']
            ]);
        });*/

        \DB::transaction(function () use ($cities) {
            $this->command->getOutput()->progressStart(count($cities));
            foreach ($cities as $data) {
                City::insert([
                    'id' => $data['id'],
                    'country_id' => $data['iso2'],
                    'name' => $data['city'],
                    'lat' => $data['lat'],
                    'lng' => $data['lng'],
                    'population' => $data['population'] !== '' ? $data['population'] : null
                ]);
                $this->command->getOutput()->progressAdvance();
            }
            $this->command->getOutput()->progressFinish();
        });
    }
}
