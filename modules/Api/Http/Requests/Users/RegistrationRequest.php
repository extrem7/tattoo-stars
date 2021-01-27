<?php

namespace Modules\Api\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'account_type_id' => ['required', 'exists:account_types,id'],
            'nickname' => [
                'required',
                'string',
                'max:255',
                'regex:/^([A-Za-z0-9_](?:(?:[A-Za-z0-9_]|(?:\.(?!\.))){0,28}(?:[A-Za-z0-9_]))?)$/'
            ],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'numbers', 'letters', 'confirmed'],
            'device' => ['required', 'string']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
