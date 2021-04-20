<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'text' => ['required', 'string255']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
