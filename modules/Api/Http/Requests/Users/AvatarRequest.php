<?php

namespace Modules\Api\Http\Requests\Users;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class AvatarRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,bmp,png', 'max:4096'],
        ];
    }

    public function uploadAvatar(): string
    {
        /* @var $user User */
        if (($user = $this->user()) && $this->hasFile('avatar')) {
            if ($uploaded = $user->uploadAvatar($this->file('avatar'))) {
                $user->load('avatarMedia');
                return $user->avatar;
            }
        }
        throw new \Exception('Error during avatar uploading');
    }

    public function authorize(): bool
    {
        return true;
    }
}
