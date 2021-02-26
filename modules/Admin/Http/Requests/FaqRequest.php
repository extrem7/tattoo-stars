<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FaqRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'question' => ['required', 'array'],
            'question.ru' => ['required'],
            'question.en' => ['required'],
            'question.*' => ['string', 'max:255'],

            'answer' => ['required', 'array'],
            'answer.ru' => ['required'],
            'answer.en' => ['required'],
            'answer.*' => ['string']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
