<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupportHelpRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string255'],
            'email' => ['required', 'email'],
            'message' => ['required', 'string255']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
