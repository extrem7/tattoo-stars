<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MessageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'message' => [Rule::requiredIf($this->file('media') === null), 'string'],
            'media' => [Rule::requiredIf(!$this->filled('message')), 'file', 'max:2048']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
