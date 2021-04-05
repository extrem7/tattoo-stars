<?php

namespace Modules\Api\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        /* @var $user User */
        $user = $this->resource;
        $user->load('avatarMedia');

        return [
            'id' => $user->id,
            'name' => $user->name,
            'nickname' => $user->nickname,
            'email' => $user->email,
            'emailVerified' => $user->hasVerifiedEmail(),
            'icon' => $this->whenAppended('icon', $user->icon),
            'avatar' => $this->whenAppended('avatar', $user->getAvatar('big')),
            'accountType' => [
                'id' => $user->accountType->id,
                'name' => $user->accountType->name
            ],
            $this->mergeWhen(
                $user->relationLoaded('information'), new InformationResource($user->information)
            ),
            'styles' => $this->when($user->relationLoaded('styles'), fn() => $user->getRelation('styles'))
        ];
    }
}
