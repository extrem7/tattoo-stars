<?php

namespace Modules\Api\Http\Controllers\Advertising;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Resources\BannerResource;
use Modules\Api\Http\Resources\PromotionResource;

final class AdvertisingController extends Controller
{
    /**
     * @api {get} /advertising List of promotions and banners
     * @apiName AdvertisingIndex
     * @apiGroup Advertising
     *
     * @apiUse Token
     *
     * @apiSuccess {Object[]} promotions Promotions.
     * @apiSuccess {Object} promotions.post Post.
     * @apiSuccess {Number} promotions.post.id Post id.
     * @apiSuccess {String} promotions.post.thumbnail Post thumbnail.
     * @apiSuccess {Number} promotions.countryId Country id.
     * @apiSuccess {Number} promotions.cityId City id.
     * @apiSuccess {String} promotions.location Location.
     * @apiSuccess {Number} promotions.clicks Clicks.
     * @apiSuccess {Number} promotions.views Views.
     * @apiSuccess {Number} promotions.budget budget.
     * @apiSuccess {Enum} promotions.accountType Account type {'users','other',null}.
     * @apiSuccess {Boolean} promotions.onPause On pause.
     * @apiSuccess {Boolean} promotions.verified Is verified.
     * @apiSuccess {String} promotions.rejectReason Reject reason.
     *
     * @apiSuccess {Object[]} banners Banners.
     * @apiSuccess {Number} banners.id Id.
     * @apiSuccess {Number} banners.userId User id.
     * @apiSuccess {String} banners.site Site.
     * @apiSuccess {String} banners.redirectToSite Redirect to site.
     * @apiSuccess {String} banners.image Image.
     * @apiSuccess {Number} banners.countryId Country id.
     * @apiSuccess {Number} banners.cityId City id.
     * @apiSuccess {String} banners.location Location.
     * @apiSuccess {Number} banners.clicks Clicks.
     * @apiSuccess {Number} banners.views Views.
     * @apiSuccess {Number} banners.budget budget.
     * @apiSuccess {Boolean} banners.onPause On pause.
     * @apiSuccess {Boolean} banners.verified Is verified.
     * @apiSuccess {String} banners.rejectReason Reject reason.
     */
    public function __invoke(): JsonResponse
    {
        $user = \Auth::user();

        $promotions = $user->promotions()->with('post')->get();
        $banners = $user->banners()->with('imageMedia')->get();

        return response()->json([
            'promotions' => PromotionResource::collection($promotions),
            'banners' => BannerResource::collection($banners)
        ]);
    }
}
