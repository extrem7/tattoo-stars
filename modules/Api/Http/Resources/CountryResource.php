<?php

namespace Modules\Api\Http\Resources;

use App\Models\Country;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    public function toArray($request): array
    {
        /* @var $country Country */
        $country = $this->resource;

        return [
            'id' => $country->id,
            'name' => $country->name
        ];
    }
}
