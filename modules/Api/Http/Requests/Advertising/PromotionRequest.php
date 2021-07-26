<?php

namespace Modules\Api\Http\Requests\Advertising;

use Illuminate\Foundation\Http\FormRequest;

class PromotionRequest extends FormRequest
{
    public function rules(): array
    {
        $update = request()->isMethod('PATCH');

        $rules = [];

        if (!$update) {
            $rules['budget'] = ['required', 'integer', 'min:1000', 'max:40000'];
        }

        return array_merge([
            'post_id' => [!$update ? 'required' : 'nullable', 'exists:posts,id'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'account_type' => ['nullable', 'in:users,other']
        ], $rules);
    }

    public function authorize(): bool
    {
        return true;
    }
}
