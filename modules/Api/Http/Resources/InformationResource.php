<?php

namespace Modules\Api\Http\Resources;

use App\Models\User\Information;
use Illuminate\Http\Resources\Json\JsonResource;

class InformationResource extends JsonResource
{
    public function toArray($request): array
    {
        /* @var $info Information */
        $info = $this->resource;

        return [
            'gender' => $info->gender,
            'birthday' => $info->birthday ? $info->birthday->format('d.m.Y') : null,
            'city' => $this->whenLoaded('city', fn() => array_merge(
                $info->city->only(['id', 'name']), ['country' => $info->city->country->only(['id', 'name'])]
            )),
            'address' => $info->address,
            'bio' => $info->bio,
            'phone' => $info->phone,
            'website' => $info->website,
            'instagram' => $info->instagram,
            'facebook' => $info->facebook
        ];
    }
}
