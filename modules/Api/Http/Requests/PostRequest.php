<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'images' => [
                Rule::requiredIf($this->file('video') === null),
                'array',
                'max:6'
            ],
            'images.*' => ['image', 'max:2048', 'dimensions:max_width=750,max_height=750'],
            'video' => [
                Rule::requiredIf(empty($this->file('images'))),
                'file',
                'mimes:mp4',
                'max:10240',
                'video_duration:30'
            ],
            'description' => ['nullable', 'string', 'max:500']
        ];
    }

    public function attributes(): array
    {
        return array_merge(['images.*' => __('validation.attributes.image')], parent::attributes());
    }

    public function authorize(): bool
    {
        return true;
    }
}
