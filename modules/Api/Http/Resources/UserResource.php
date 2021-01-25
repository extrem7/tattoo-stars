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
        $user->append('icon');

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ];
    }
}
