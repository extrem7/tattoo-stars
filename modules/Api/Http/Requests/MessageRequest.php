<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MessageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'message' => [
                Rule::requiredIf($this->file('image') === null && $this->file('video') === null),
                'string'
            ],
            'image' => [Rule::requiredIf(!($this->filled('message') || $this->file('video'))), 'image', 'max:2048',],
            'video' => [
                Rule::requiredIf(!($this->filled('message') || $this->file('image'))),
                'file',
                'mimes:mp4',
                'max:10240',
                'video_duration:35'
            ]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
