<?php

namespace Modules\Api\Http\Requests\Users;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'device' => ['required', 'string']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
