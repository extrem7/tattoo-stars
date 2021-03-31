<?php

namespace Modules\Api\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    public function toArray($request): array
    {
        /* @var $user User */
        $user = $this->resource;
        $info = $user->information;

        $location = null;
        if ($city = $info->city) {
            $location = "{$city->country->name}, $city->name, $info->address";
        }

        $inSubscriptions = null;
        if (\Auth::id() !== $user->id) {
            $inSubscriptions = \Auth::user()->subscriptions()->where('user_id', '=', $user->id)->exists();
        }

        return [
            'id' => $user->id,
            'avatar' => $user->avatar,
            'name' => $user->name,
            'location' => $location,
            'bio' => $info->bio,
            'inSubscriptions' => $this->when($inSubscriptions !== null, $inSubscriptions),

            'postsCount' => $user->posts()->count(),
            'subscribersCount' => $user->subscribers()->count(),
            'subscriptionsCount' => $user->subscriptions()->count(),

            'phone' => $info->phone,
            'website' => $info->website,
            'instagram' => $info->instagram,
            'facebook' => $info->facebook
        ];
    }
}
