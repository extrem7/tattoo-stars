<?php

namespace Modules\Api\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'account_type_id' => ['required', 'exists:account_types,id'],
            'nickname' => ['required', 'string255', 'regex:' . config('tattoo.nickname_regex')],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', ...config('tattoo.password_rules')],
            'device' => ['required', 'string']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
