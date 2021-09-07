<?php

namespace Modules\Api\Http\Controllers\Advertising;

use App\Http\Controllers\Controller;
use App\Models\Advertising\Promotion;
use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Requests\Advertising\PromotionRequest;
use Modules\Api\Http\Resources\PromotionResource;

final class PromotionController extends Controller
{
    /**
     * @api {post} /advertising/promotion Create promotion for post.
     * @apiName AdvertisingPromotionCreate
     * @apiGroup Advertising
     *
     * @apiUse Token
     *
     * @apiParam {Number} post_id Post id.
     * @apiParam {Number} [country_id] Country id.
     * @apiParam {Number} [city_id] City id.
     * @apiParam {Enum} [account_type] Account type {'users','other',null}.
     * @apiParam {Number} budget Budget.
     *
     * @apiSuccess {String} message Promotion status.
     * @apiSuccess {Number} id Promotion id.
     */
    public function store(PromotionRequest $request): JsonResponse
    {
        $request->validatePostAndCity();

        $promotion = Promotion::create($request->validated());

        // todo billing stuff
        //$promotion->verified = true;
        //$promotion->save();

        return response()->json([
            'message' => 'Promotion for your post has been created.',
            'id' => $promotion->id
        ]);
    }

    /**
     * @api {get} /advertising/promotion/:id Show promotion.
     * @apiName AdvertisingPromotionShow
     * @apiGroup Advertising
     *
     * @apiUse Token
     *
     * @apiSuccess {Object} post Post.
     * @apiSuccess {Number} post.id Post id.
     * @apiSuccess {String} post.thumbnail Post thumbnail.
     * @apiSuccess {Number} countryId Country id.
     * @apiSuccess {Number} cityId City id.
     * @apiSuccess {String} location Location.
     * @apiSuccess {Number} clicks Clicks.
     * @apiSuccess {Number} views Views.
     * @apiSuccess {Number} budget budget.
     * @apiSuccess {Enum} accountType Account type {'users','other',null}.
     * @apiSuccess {Boolean} onPause On pause.
     * @apiSuccess {Boolean} verified Is verified {true,false,null}.
     * @apiSuccess {String} rejectReason Reject reason.
     */
    public function show(Promotion $promotion): JsonResponse
    {
        return response()->json(new PromotionResource($promotion));
    }

    /**
     * @api {patch} /advertising/promotion/:id Update promotion.
     * @apiName AdvertisingPromotionUpdate
     * @apiGroup Advertising
     *
     * @apiUse Token
     *
     * @apiParam {Number} [post_id] Post id.
     * @apiParam {Number} [country_id] Country id.
     * @apiParam {Number} [city_id] City id.
     * @apiParam {Enum} [account_type] Account type {'users','other',null}.
     *
     * @apiSuccess {String} message Promotion update status.
     */
    public function update(PromotionRequest $request, Promotion $promotion): JsonResponse
    {
        $request->validatePostAndCity();

        $promotion->update($request->validated());
        $promotion->verified = null;
        $promotion->reject_reason = null;
        $promotion->save();

        return response()->json(['message' => 'Promotion has been updated.']);
    }

    /**
     * @api {post} /advertising/promotion/:id/pause Pause promotion.
     * @apiName AdvertisingPromotionPause
     * @apiGroup Advertising
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message Promotion pause toggle status.
     * @apiSuccess {Boolean} status Promotion pause status.
     */
    public function pause(Promotion $promotion): JsonResponse
    {
        $status = $promotion->on_pause ? null : true;
        $promotion->on_pause = $status;
        $promotion->save();

        return response()->json([
            'message' => 'Promotion has been ' . ($status ? 'paused' : 'resumed') . '.',
            'status' => (bool)$status
        ]);
    }

    /**
     * @api {post} /advertising/promotion/:id/click Increment promotion clicks.
     * @apiName AdvertisingPromotionClick
     * @apiGroup Advertising
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message Promotion clicks incrementing status.
     */
    public function click(Promotion $promotion): JsonResponse
    {
        abort_if($promotion->views >= $promotion->budget, 400, 'This promotion is inactive.');

        $promotion->increment('clicks');

        return response()->json(['message' => 'Click has been stored.']);
    }

    /**
     * @api {delete} /advertising/promotion/:id/ Delete promotion
     * @apiName AdvertisingPromotionDelete
     * @apiGroup Advertising
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message Is promotion deleted message.
     */
    public function destroy(Promotion $promotion): JsonResponse
    {
        $promotion->delete();

        return response()->json(['message' => 'Promotion has been deleted.']);
    }

}
