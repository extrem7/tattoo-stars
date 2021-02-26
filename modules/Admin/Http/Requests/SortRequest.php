<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SortRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'order' => ['required', 'array'],
            'order.*' => ['integer']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
