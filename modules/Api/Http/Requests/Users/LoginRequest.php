<?php

namespace Modules\Api\Http\Requests\Users;

use App\Http\Requests\LoginRequest as BaseLoginRequest;
use Auth;

class LoginRequest extends BaseLoginRequest
{
    public function rules(): array
    {
        return array_merge([
            'email_or_nickname' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device' => ['required', 'string']
        ]);
    }

    public function try(): bool
    {
        $emailOrNickname = $this->email_or_nickname;
        $field = filter_var($emailOrNickname, FILTER_VALIDATE_EMAIL) ? 'email' : 'nickname';
        return Auth::once([
            $field => $emailOrNickname,
            'password' => $this->password
        ]);

    }

    public function throttleKey(): string
    {
        return 'api:login-' . $this->ip();
    }
}
