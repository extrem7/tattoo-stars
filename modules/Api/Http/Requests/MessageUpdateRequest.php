<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'message' => ['required', 'string']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
