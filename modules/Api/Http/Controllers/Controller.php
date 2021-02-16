<?php

namespace Modules\Api\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Modules\Api\Http\Resources\UserResource;

class Controller extends BaseController
{
    use ValidatesRequests;

    protected function getUser(User $user): array
    {
        return ['user' => new UserResource($user)];
    }

    protected function getUserWithToken(User $user, string $device = null): array
    {
        $token = $user->createToken($device ?? $user->currentAccessToken()->name);
        return array_merge($this->getUser($user), ['token' => $token->plainTextToken]);
    }
}
