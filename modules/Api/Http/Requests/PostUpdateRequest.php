<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'description' => ['nullable', 'string', 'max:500']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
