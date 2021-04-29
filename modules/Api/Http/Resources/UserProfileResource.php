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
        $authUser = \Auth::user();

        $location = null;
        if ($city = $info->city) {
            $location = "{$city->country->name}, $city->name, $info->address";
        }

        $inSubscriptions = null;
        $inBlacklist = null;

        $selfView = $authUser->id === $user->id;

        if (!$selfView) {
            $inSubscriptions = $authUser->subscriptions()->where('user_id', '=', $user->id)->exists();
            $inBlacklist = $authUser->blacklist()->where('blocked_id', '=', $user->id)->exists();
        }

        return [
            'id' => $user->id,
            'avatar' => $user->getAvatar('big'),
            'name' => $user->name,
            'nickname' => $user->nickname,
            'location' => $location,
            'bio' => $info->bio,
            $this->mergeWhen(!$selfView, [
                'inSubscriptions' => $inSubscriptions,
                'inBlacklist' => $inBlacklist,
            ]),


            'postsCount' => $user->posts()->count(),
            'subscribersCount' => $user->subscribers()->count(),
            'subscriptionsCount' => $user->subscriptions()->count(),

            'phone' => $info->phone,
            'email' => $user->email,
            'website' => $info->website,
            'instagram' => $info->instagram,
            'facebook' => $info->facebook
        ];
    }
}
