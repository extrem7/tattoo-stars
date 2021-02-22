<?php

namespace Modules\Api\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use Torann\GeoIP\Location;

/**
 * @group Cities
 */
class CitiesController extends Controller
{
    /**
     * @api {get} /countries List of countries with search like /countries/{query?}
     * @apiName GetCountries
     * @apiGroup Cities
     *
     * @apiUse Token
     *
     * @apiSuccess {String} id Country id.
     * @apiSuccess {String} name Country name.
     */
    public function countries(string $query = null): array
    {
        $countries = Country::when($query, fn($q) => $q->where('name', 'like', "$query%"))
            ->get(['id', app()->getLocale() === 'ru' ? 'name_ru' : 'name'])
            ->sortByDesc(fn(Country $c) => in_array($c->name, [
                'Ukraine', 'Russia', 'Belarus', 'Украина', 'Россия', 'Беларусь'
            ]))
            ->map(fn(Country $c) => $c->only(['id', 'name']));

        return $countries->toArray();
    }

    /**
     * @api {get} /cities List of cities with search like /cities/{CountryId}/{query?}
     * @apiName GetCities
     * @apiGroup Cities
     *
     * @apiUse Token
     *
     * @apiSuccess {String} id City id.
     * @apiSuccess {String} name City name.
     */
    public function cities(string $country, string $query = null): array
    {
        $translatedField = app()->getLocale() === 'ru' ? 'name_ru' : 'name';

        $countries = City::where('country_id', '=', strtoupper($country))
            ->when($query, fn($q) => $q->where($translatedField, 'like', "$query%"))
            ->biggest()
            ->limit(20)
            ->get(['id', $translatedField])
            ->map(fn(City $c) => $c->only(['id', 'name']));

        return $countries->toArray();
    }

    /**
     * @api {get} /geoip GEOIP - get user country
     * @apiName GeoIP
     * @apiGroup Cities
     *
     * @apiUse Token
     *
     * @apiSuccess {Number} country Country id.
     * @apiSuccess {Object} city City.
     * @apiSuccess {Object} city.id City id.
     * @apiSuccess {Object} city.name City name.
     */
    public function geoip(): array
    {
        $geoip = [
            'country' => null,
            'city' => null
        ];

        try {
            $location = config('app.env') === 'local' ? geoip(config('api.local_geoip')) : geoip()->getLocation();
            if ($location instanceof Location) {
                $code = $location->iso_code;
                if ($country = Country::where('id', '=', $code)->first()) {
                    $geoip['country'] = $country->id;
                    if ($city = $country->cities()
                        ->where('name', 'like', "$location->city%")
                        ->biggest()
                        ->first(['id', 'name'])) {
                        $geoip['city'] = $city->only(['id', 'name']);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error during geoip: ' . $e->getMessage());
        }

        return $geoip;
    }
}
