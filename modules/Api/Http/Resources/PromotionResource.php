<?php

namespace Modules\Api\Http\Resources;

use App\Models\Advertising\Promotion;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
{
    public function toArray($request): array
    {
        /* @var $promo Promotion */
        $promo = $this->resource;

        $location = null;
        if ($city = $promo->city) {
            $location = "$city->name, {$city->country->name}";
        }

        if ($video = $promo->post->videoMedia) {
            $thumbnail = $video->getFullUrl('thumbnail');
        } else {
            $thumbnail = $this->post->imagesMedia()->first()->getFullUrl('thumb');
        }

        return [
            'id' => $promo->id,
            'post' => [
                'id' => $promo->post_id,
                'thumbnail' => $thumbnail
            ],
            'countryId' => $promo->country_id,
            'cityId' => $promo->city_id,
            'location' => $this->when($location, $location),
            'clicks' => $promo->clicks,
            'views' => $promo->views,
            'budget' => $promo->budget,
            'accountType' => $promo->account_type,
            'onPause' => (bool)$promo->on_pause,
            'verified' => (bool)$promo->verified,
            'rejectReason' => $promo->reject_reason
        ];
    }
}
