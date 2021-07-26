<?php

namespace Modules\Api\Http\Controllers\Advertising;

use App\Http\Controllers\Controller;
use App\Models\Advertising\Promotion;
use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Requests\Advertising\PromotionRequest;
use Modules\Api\Http\Resources\PromotionResource;

class PromotionController extends Controller
{
    public function store(PromotionRequest $request): JsonResponse
    {
        $request->validatePostAndCity();

        $promotion = Promotion::create($request->validated());

        // todo billing stuff
        $promotion->verified = true;
        $promotion->save();

        return response()->json([
            'message' => 'Promotion for your post has been created.',
            'id' => $promotion->id
        ]);
    }

    public function show(Promotion $promotion): JsonResponse
    {
        return response()->json(new PromotionResource($promotion));
    }

    public function update(PromotionRequest $request, Promotion $promotion): JsonResponse
    {
        $request->validatePostAndCity();

        $promotion->update($request->validated());
        $promotion->verified = null;
        $promotion->save();

        return response()->json(['message' => 'Promotion has been updated.']);
    }

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

    public function click(Promotion $promotion): JsonResponse
    {
        abort_if($promotion->views >= $promotion->budget, 400, 'This promotion is inactive.');

        $promotion->increment('clicks');

        return response()->json(['message' => 'Click has been stored.']);
    }
}
