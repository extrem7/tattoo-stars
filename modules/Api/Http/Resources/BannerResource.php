<?php

namespace Modules\Api\Http\Resources;

use App\Models\Advertising\Banner;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    public function toArray($request): array
    {
        /* @var $banner Banner */
        $banner = $this->resource;

        $location = null;
        if ($city = $banner->city) {
            $location = "$city->name, {$city->country->name}";
        }

        return array_merge([
            'id' => $banner->id,
            'userId' => $banner->user_id,
            'site' => $banner->user->information->website,
            'redirectToSite' => (bool)$banner->redirect_to_site,
            'image' => $banner->imageMedia->getFullUrl(),
            'countryId' => $this->when($banner->country_id, fn() => $banner->country_id),
            'cityId' => $this->when($banner->city_id, fn() => $banner->city_id),
            'location' => $this->when($location, $location)
        ], \Auth::id() === $banner->user_id ? [
            'clicks' => $banner->clicks,
            'views' => $banner->views,
            'budget' => $banner->budget,
            'onPause' => (bool)$banner->on_pause,
            'verified' => (bool)$banner->verified,
            'rejectReason' => $banner->reject_reason
        ] : null);
    }
}
