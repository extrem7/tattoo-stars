<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'reason' => ['required', 'string255']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
