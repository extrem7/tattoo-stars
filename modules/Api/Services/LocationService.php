<?php

namespace Modules\Api\Services;

use App\Models\Country;
use Torann\GeoIP\GeoIP;
use Torann\GeoIP\Location;

class LocationService
{
    public function geolocate(): array
    {
        $geoip = [
            'country' => null,
            'city' => null
        ];

        try {
            $location = $this->getLocation();
            if ($location instanceof Location) {
                $code = $location->iso_code;
                if ($country = Country::where('id', '=', $code)->first()) {
                    $geoip['country'] = $country->id;
                    if ($city = $country->cities()
                        ->where('name', 'like', "$location->city%")
                        ->biggest()
                        ->first(['id', $this->localizedColumn()])) {
                        $geoip['city'] = $city->only(['id', 'name']);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error during geoip: ' . $e->getMessage());
        }

        return $geoip;
    }

    /* @return GeoIP|Location */
    protected function getLocation()
    {
        return app()->environment('local') ? geoip(config('api.local_geoip')) : geoip()->getLocation();
    }

    public function localizedColumn(): string
    {
        return app()->getLocale() === 'ru' ? 'name_ru' : 'name';
    }
}
