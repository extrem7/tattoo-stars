<?php

namespace Modules\Api\Repositories;

use App\Models\City;
use App\Models\Country;
use Illuminate\Support\Collection;
use Modules\Api\Services\LocationService;

class LocationRepository
{
    protected LocationService $service;

    public function __construct()
    {
        $this->service = app(LocationService::class);
    }

    public function getCountriesByName(string $name = null): Collection
    {
        return Country::when($name, fn($q) => $q->search($name))
            ->get(['id', $this->service->localizedColumn()])
            ->sortByDesc(fn(Country $c) => in_array($c->id, ['UA', 'RU', 'BY']));
    }

    public function getCitiesByCountryAndName(string $country, string $name = null): Collection
    {
        $translatedColumn = $this->service->localizedColumn();

        return City::where('country_id', '=', strtoupper($country))
            ->when($name, fn($q) => $q->where($translatedColumn, 'like', "$name%"))
            ->biggest()
            ->limit(20)
            ->get(['id', $translatedColumn]);
    }
}
