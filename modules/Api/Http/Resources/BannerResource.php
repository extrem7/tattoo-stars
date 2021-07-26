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

        return [
            'id' => $banner->id,
            'user_id' => $banner->user_id,
            'site' => $banner->user->information->website,
            'redirectToSite' => (bool)$banner->redirect_to_site,
            'image' => $banner->imageMedia->getFullUrl(),
            'countryId' => $this->when($banner->country_id, fn() => $banner->country_id),
            'cityId' => $this->when($banner->city_id, fn() => $banner->city_id),
            'location' => $this->when($location, $location),
            'clicks' => $this->when($banner->clicks, fn() => $banner->clicks),
            'views' => $this->when($banner->views, fn() => $banner->views),
            'budget' => $this->when($banner->budget, fn() => $banner->budget),
            'onPause' => $this->when((bool)$banner->on_pause, fn() => $banner->on_pause),
            'verified' => $this->when((bool)$banner->verified, fn() => $banner->verified)
        ];
    }
}
