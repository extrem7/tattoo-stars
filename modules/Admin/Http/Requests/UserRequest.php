<?php

namespace Modules\Admin\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function rules(): array
    {
        $update = request()->isMethod('PATCH');

        return [
            'name' => ['required', 'string', 'max:255'],
            'nickname' => [
                'required',
                'string',
                'max:255',
                'regex:/^([A-Za-z0-9_](?:(?:[A-Za-z0-9_]|(?:\.(?!\.))){0,28}(?:[A-Za-z0-9_]))?)$/'
            ],
            'account_type_id' => ['required', 'exists:account_types,id'],
            'email' => ['required', 'email', 'unique:users,email,' . ($update ? $this->route()->parameter('user') : '')],
            'password' => [$update ? 'nullable' : 'required', 'string', 'min:8'],
            'roles' => ['array'],
            'roles.*' => ['nullable', 'numeric', 'exists:user_roles,id']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
