<?php

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Resources\CityResource;
use Modules\Api\Http\Resources\CountryResource;
use Modules\Api\Repositories\LocationRepository;
use Modules\Api\Services\LocationService;

/**
 * @group Cities
 */
final class CitiesController extends Controller
{
    protected LocationService $service;
    protected LocationRepository $repository;

    public function __construct(LocationService $service, LocationRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

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
    public function countries(string $query = null): JsonResponse
    {
        $countries = $this->repository->getCountriesByName($query);

        return response()->json(CountryResource::collection($countries));
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
    public function cities(string $country, string $query = null): JsonResponse
    {
        $cities = $this->repository->getCitiesByCountryAndName($country, $query);

        return response()->json(CityResource::collection($cities));
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
        return $this->service->geolocate();
    }
}
