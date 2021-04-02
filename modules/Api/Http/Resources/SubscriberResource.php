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

        return [
            'id' => $user->id,
            'avatar' => $user->getAvatar('medium'),
            'name' => $user->name,
            'nickname' => $user->nickname,
            'location' => $this->when($info->relationLoaded('city') && $info->city, fn() => $info->city->name),
        ];
    }
}
