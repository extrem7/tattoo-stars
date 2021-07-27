<?php

namespace Modules\Api\Http\Controllers\Advertising;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Resources\BannerResource;
use Modules\Api\Http\Resources\PromotionResource;

class AdvertisingController extends Controller
{
    public function index(): JsonResponse
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
