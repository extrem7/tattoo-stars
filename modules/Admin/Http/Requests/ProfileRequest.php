<?php

namespace Modules\Admin\Http\Requests;

use App\Models\User;
use Auth;
use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'nickname' => [
                'required',
                'string',
                'max:255',
                'regex:/^([A-Za-z0-9_](?:(?:[A-Za-z0-9_]|(?:\.(?!\.))){0,28}(?:[A-Za-z0-9_]))?)$/'
            ],
            'email' => ['required', 'email', 'unique:users,email,' . Auth::id()],
            'password' => ['nullable', 'string', 'min:8']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
