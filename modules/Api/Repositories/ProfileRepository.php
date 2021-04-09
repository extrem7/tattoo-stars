<?php

namespace Modules\Api\Repositories;

use App\Models\User;
use App\Models\User\AccountType;
use App\Models\User\Gender;
use App\Models\User\Style;
use Illuminate\Database\Eloquent\Collection;
use Modules\Api\Http\Resources\UserResource;

class ProfileRepository
{
    public function getAccountTypes(): Collection
    {
        return AccountType::all(['id', 'name']);
    }

    public function getGenders(): Collection
    {
        return Gender::all();
    }

    public function getStyles(): Collection
    {
        return Style::all();
    }

    public function getUserWithToken(User $user, string $device = null): array
    {
        $token = $user->createToken($device ?? $user->currentAccessToken()->name);
        return array_merge($this->getUser($user), ['token' => $token->plainTextToken]);
    }

    public function getUser(User $user): array
    {
        return ['user' => new UserResource($user)];
    }

    public function update(User $user, ?array $styles, $information): void
    {
        if (!empty($styles)) {
            $user->styles()->sync($styles);
        }
        $user->information->update($information);
    }

    public function destroy(): bool
    {
        $user = \Auth::user();
        $user->update(['deleted_self' => true]);
        return $user->delete();
    }
}
