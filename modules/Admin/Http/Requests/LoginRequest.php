<?php

namespace Modules\Admin\Http\Requests;

use App\Http\Requests\LoginRequest as BaseLoginRequest;

class LoginRequest extends BaseLoginRequest
{
    public function throttleKey(): string
    {
        return 'admin:login-' . $this->ip();
    }
}
