<?php

namespace Modules\Api\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'account_type_id' => ['required', 'exists:account_types,id', 'not_in:1'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'style_id' => ['nullable', 'exists:user_styles,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
