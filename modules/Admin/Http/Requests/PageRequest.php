<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'array'],
            'title.ru' => ['required'],
            'title.en' => ['required'],
            'title.*' => ['string', 'max:255'],

            'body' => ['required', 'array'],
            'body.ru' => ['required'],
            'body.en' => ['required'],
            'body.*' => ['string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
