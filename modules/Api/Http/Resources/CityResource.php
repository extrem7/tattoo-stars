<?php

namespace Modules\Api\Http\Resources;

use App\Models\City;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    public function toArray($request): array
    {
        /* @var $city City */
        $city = $this->resource;

        return [
            'id' => $city->id,
            'name' => $city->name
        ];
    }
}
