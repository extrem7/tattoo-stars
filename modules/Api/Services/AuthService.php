<?php

namespace Modules\Api\Services;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Modules\Api\Notifications\ResetPassword;

class AuthService
{
    public function register(array $data, string $password)
    {
        $hash = \Hash::make($password);

        $user = User::create(array_merge($data, [
            'name' => $data['nickname'],
            'password' => $hash
        ]));

        event(new Registered($user));

        $user->information()->create();

        return $user;
    }

    public function resetPassword(string $email): void
    {
        if ($user = User::where('email', '=', $email)->first()) {
            $password = \Str::random(8);
            if ($user->update(['password' => \Hash::make($password)])) {
                $user->notify(new ResetPassword($password));
                $user->tokens()->delete();
            }
        }
    }
}
