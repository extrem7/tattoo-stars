<?php

namespace Modules\Api\Http\Requests\Advertising;

use App\Models\City;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class PromotionRequest extends FormRequest
{
    public function rules(): array
    {
        $update = request()->isMethod('PATCH');

        $rules = [];

        if (!$update) {
            $rules['budget'] = ['required', 'integer', 'min:1000', 'max:40000'];
        }

        return array_merge([
            'post_id' => [!$update ? 'required' : 'nullable', 'exists:posts,id'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'account_type' => ['nullable', 'in:users,other']
        ], $rules);
    }

    public function validatePostAndCity(): void
    {
        $post = Post::find($this->input('post_id'));
        $city = City::find($this->input('city_id'));

        abort_unless(
            !$post || $post->user_id === \Auth::id(),
            Response::HTTP_FORBIDDEN,
            'You are can\'t promote someone else\'s post.'
        );
        abort_if(
            $city && $this->filled('country_id') && $city->country_id !== $this->input('country_id'),
            Response::HTTP_BAD_REQUEST,
            'City must belongs to chosen country.'
        );
    }

    public function authorize(): bool
    {
        return true;
    }
}
