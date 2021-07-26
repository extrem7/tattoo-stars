<?php

namespace Modules\Api\Http\Controllers\Advertising;

use App\Http\Controllers\Controller;
use App\Models\Advertising\Banner;
use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Requests\Advertising\BannerRequest;
use Modules\Api\Http\Resources\BannerResource;

class BannerController extends Controller
{
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

    public function show(Banner $banner): JsonResponse
    {
        return response()->json(new BannerResource($banner));
    }

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

    public function click(Banner $banner): JsonResponse
    {
        abort_if($banner->views >= $banner->budget, 400, 'This Banner is inactive.');

        $banner->increment('clicks');

        return response()->json(['message' => 'Click has been stored.']);
    }
}
