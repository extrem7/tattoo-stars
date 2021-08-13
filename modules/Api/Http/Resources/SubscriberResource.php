<?php

namespace Modules\Api\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriberResource extends JsonResource
{
    public function toArray($request): array
    {
        /* @var $user User */
        $user = $this->resource;
        $info = $user->information;

        $location = null;
        if ($info && $info->relationLoaded('city') && $city = $info->city) {
            $location = "$city->name, {$city->country->name}";
        }

        return [
            'id' => $user->id,
            'avatar' => $user->getAvatar('medium'),
            'name' => $user->name,
            'nickname' => $user->nickname,
            'location' => $this->when($location, $location),
            'winner' => $user->contestWorks->isNotEmpty(),
        ];
    }
}
