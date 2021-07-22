<?php

namespace Modules\Api\Http\Requests\Advertising;

use Illuminate\Foundation\Http\FormRequest;

class TopRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'days' => ['required', 'integer', 'in:10,30']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
