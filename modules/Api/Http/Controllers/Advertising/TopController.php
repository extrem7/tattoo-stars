<?php

namespace Modules\Api\Http\Controllers\Advertising;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Requests\Advertising\TopRequest;

final class TopController extends Controller
{
    /**
     * @api {get} /advertising/top Pin to top account status
     * @apiName AdvertisingTopStatus
     * @apiGroup Advertising
     *
     * @apiUse Token
     *
     * @apiSuccess {Boolean} status Top account status.
     * @apiSuccess {Date} until Top account until.
     */
    public function status(): JsonResponse
    {
        $top = \Auth::user()->tops()->active()->first();

        $response = [
            'status' => $top !== null
        ];

        if ($top) {
            $response['until'] = $top->end_at->format('d.m.Y');
        }

        return response()->json($response);
    }

    /**
     * @api {post} /advertising/top Pin account to top
     * @apiName AdvertisingTopPin
     * @apiGroup Advertising
     *
     * @apiUse Token
     *
     * @apiParam {Number} days Pin for days {7,15,30}.
     *
     * @apiSuccess {String} message Pin status.
     */
    public function pin(TopRequest $request): JsonResponse
    {
        $days = $request->input('days');

        $top = \Auth::user()->tops()->create(['days' => $days]);

        // todo billing stuff
        $top->start_at = now();
        $top->end_at = now()->addDays($days);
        $top->save();

        return response()->json(['message' => 'Your account has been pinned to top.']);
    }
}
