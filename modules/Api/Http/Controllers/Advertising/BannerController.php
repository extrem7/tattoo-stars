<?php

namespace Modules\Api\Http\Controllers\Advertising;

use App\Http\Controllers\Controller;
use App\Models\Advertising\Banner;
use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Requests\Advertising\BannerRequest;
use Modules\Api\Http\Resources\BannerResource;

final class BannerController extends Controller
{
    /**
     * @api {post} /advertising/banner Create banner.
     * @apiName AdvertisingBannerCreate
     * @apiGroup Advertising
     *
     * @apiUse Token
     *
     * @apiParam {File} image Image less 4096kb.
     * @apiParam {Boolean} [redirect_to_site] Redirect to user's site.
     * @apiParam {Number} [country_id] Country id.
     * @apiParam {Number} [city_id] City id.
     * @apiParam {Number} budget Budget.
     *
     * @apiSuccess {String} message Banner status.
     * @apiSuccess {Number} id Banner id.
     */
    public function store(BannerRequest $request): JsonResponse
    {
        $request->validateCity();

        $banner = null;
        \DB::transaction(function () use ($request, &$banner) {
            $banner = \Auth::user()->banners()->create($request->validated());
            $banner->uploadImage($request->file('image'));
        });

        // todo billing stuff
        $banner->verified = true;
        $banner->save();

        return response()->json([
            'message' => 'Banner has been created.',
            'id' => $banner->id
        ]);
    }

    /**
     * @api {get} /advertising/banner/:id Show banner.
     * @apiName AdvertisingBannerShow
     * @apiGroup Advertising
     *
     * @apiUse Token
     * @apiSuccess {Number} id Id.
     * @apiSuccess {Number} userId User id.
     * @apiSuccess {String} site Site.
     * @apiSuccess {String} redirectToSite Redirect to site.
     * @apiSuccess {String} image Image.
     * @apiSuccess {Number} countryId Country id.
     * @apiSuccess {Number} cityId City id.
     * @apiSuccess {String} location Location.
     * @apiSuccess {Number} clicks Clicks.
     * @apiSuccess {Number} views Views.
     * @apiSuccess {Number} budget budget.
     * @apiSuccess {Boolean} onPause On pause.
     * @apiSuccess {Boolean} verified Is verified.
     * @apiSuccess {String} rejectReason Reject reason.
     */
    public function show(Banner $banner): JsonResponse
    {
        return response()->json(new BannerResource($banner));
    }

    /**
     * @api {patch} /advertising/banner Update banner.
     * @apiName AdvertisingBannerUpdate
     * @apiGroup Advertising
     *
     * @apiUse Token
     *
     * @apiParam {File} [image] Image less 4096kb.
     * @apiParam {Boolean} [redirect_to_site] Redirect to user's site.
     * @apiParam {Number} [country_id] Country id.
     * @apiParam {Number} [city_id] City id.
     *
     * @apiSuccess {String} message Banner update status.
     */
    public function update(BannerRequest $request, Banner $banner): JsonResponse
    {
        $request->validateCity();

        if ($file = $request->file('image')) {
            \DB::transaction(fn() => $banner->uploadImage($file));
        }

        $banner->update($request->validated());
        $banner->verified = null;
        $banner->save();

        return response()->json(['message' => 'Banner has been updated.']);
    }

    /**
     * @api {post} /advertising/banner/:id/pause Pause banner.
     * @apiName AdvertisingBannerPause
     * @apiGroup Advertising
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message Banner pause toggle status.
     * @apiSuccess {Boolean} status Banner pause status.
     */
    public function pause(Banner $banner): JsonResponse
    {
        $status = $banner->on_pause ? null : true;
        $banner->on_pause = $status;
        $banner->save();

        return response()->json([
            'message' => 'Banner has been ' . ($status ? 'paused' : 'resumed') . '.',
            'status' => (bool)$status
        ]);
    }

    /**
     * @api {post} /advertising/banner/:id/click Increment banner clicks.
     * @apiName AdvertisingBannerClick
     * @apiGroup Advertising
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message Banner clicks incrementing status.
     */
    public function click(Banner $banner): JsonResponse
    {
        abort_if($banner->views >= $banner->budget, 400, 'This Banner is inactive.');

        $banner->increment('clicks');

        return response()->json(['message' => 'Click has been stored.']);
    }
}
