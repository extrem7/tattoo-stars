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
            'email' => ['required', 'email', 'unique:users,email,' . ($update ? $this->user->id : '')],
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
