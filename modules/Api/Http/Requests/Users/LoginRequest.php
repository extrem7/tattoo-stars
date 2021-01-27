<?php

namespace Modules\Api\Http\Requests\Users;

use App\Http\Requests\LoginRequest as BaseLoginRequest;
use Auth;

class LoginRequest extends BaseLoginRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'device' => ['required', 'string']
        ]);
    }

    public function try(): bool
    {
        return Auth::once($this->only('email', 'password'));
    }

    public function throttleKey(): string
    {
        return 'api:login-' . $this->ip();
    }
}
