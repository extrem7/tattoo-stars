<?php

namespace Modules\Api\Http\Requests\Users;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nickname' => ['nullable', 'string255', 'regex:' . config('tattoo.nickname_regex')],
            'name' => ['nullable', 'string255'],

            'gender_id' => ['nullable', 'exists:user_genders,id'],
            'birthday' => ['nullable', 'date'],

            'city_id' => ['nullable', 'exists:cities,id'],
            'address' => ['nullable', 'string255'],

            'styles' => ['nullable', 'array', 'max:5'],
            'styles.*' => ['exists:user_styles,id'],
            'bio' => ['nullable', 'string', 'max:120'],

            'phone' => ['nullable', 'string', 'regex:/\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/'],
            'email' => ['nullable', 'email', 'unique:users,email,' . $this->user()->id],
            'password' => ['nullable', config('tattoo.password_rules')],

            'website' => ['nullable', 'active_url'],
            'instagram' => [
                'nullable',
                'url',
                'regex:/(?:(?:http|https):\/\/)?(?:www.)?(?:instagram.com|instagr.am)\/([A-Za-z0-9-_]+)/im',
            ],
            'facebook' => [
                'nullable',
                'url',
                'regex:/(?:(?:http|https):\/\/)?(?:www.)?facebook.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[?\w\-]*\/)?(?:profile.php\?id=(?=\d.*))?([\w\-]*)?/',
            ]
        ];
    }

    public function all($keys = null)
    {
        $data = parent::all($keys);
        if ($this->filled('birthday')) {
            $data['birthday'] = Carbon::parse($data['birthday'])->format('Y-m-d');
        }
        return $data;
    }

    public function authorize(): bool
    {
        return true;
    }
}