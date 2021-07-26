<?php

namespace Modules\Api\Http\Requests\Advertising;

use App\Models\City;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class BannerRequest extends FormRequest
{
    public function rules(): array
    {
        $update = request()->isMethod('PATCH');

        $rules = [];

        if (!$update) {
            $rules['budget'] = ['required', 'integer', 'min:1000', 'max:40000'];
        }

        return array_merge([
            'image' => [!$update ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,bmp,png,gif', 'max:4096'],
            'redirect_to_site' => ['nullable', 'bool'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'account_type' => ['nullable', 'in:users,other']
        ], $rules);
    }

    public function validateCity(): void
    {
        $city = City::find($this->input('city_id'));

        abort_if(
            $city && $this->filled('country_id') && $city->country_id !== $this->input('country_id'),
            Response::HTTP_BAD_REQUEST,
            'City must belongs to chosen country.'
        );
    }

    public function authorize(): bool
    {
        return true;
    }
}
