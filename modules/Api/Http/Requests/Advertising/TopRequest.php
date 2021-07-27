<?php

namespace Modules\Api\Http\Requests\Advertising;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class TopRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'days' => ['required', 'integer', 'in:7,15,30']
        ];
    }

    public function authorize(): bool
    {
        abort_unless(
            in_array($this->user()->account_type_id, [3, 4], true),
            Response::HTTP_FORBIDDEN,
            'Pin to top is now allowed for your account type.'
        );

        abort_if(
            $this->user()->tops()->active()->exists(),
            Response::HTTP_CONFLICT,
            'You are already have top account.'
        );

        return true;
    }
}
