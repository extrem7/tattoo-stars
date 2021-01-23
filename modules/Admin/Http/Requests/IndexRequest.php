<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'searchQuery' => ['nullable', 'string'],
            'sortBy' => ['nullable', 'string'],
            'sortDesc' => ['nullable', 'bool']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
