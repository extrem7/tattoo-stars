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
            'email' => $user->email,
            'emailVerified' => $user->hasVerifiedEmail(),
            'avatar' => $this->whenLoaded('avatarMedia', $user->icon),
            'accountType' => [
                'id' => $user->account_type_id,
                'label' => $user->accountType->label
            ]
        ];
    }
}
