<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'query' => ['nullable', 'string255']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
