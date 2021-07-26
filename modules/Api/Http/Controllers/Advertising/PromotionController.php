<?php

namespace Modules\Api\Http\Controllers\Advertising;

use App\Http\Controllers\Controller;
use App\Models\Advertising\Promotion;
use App\Models\City;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Modules\Api\Http\Requests\Advertising\PromotionRequest;
use Modules\Api\Http\Resources\PromotionResource;

class PromotionController extends Controller
{
    public function store(PromotionRequest $request): JsonResponse
    {
        $this->validatePromotion($request);

        $promotion = Promotion::create($request->validated());

        // todo billing stuff
        $promotion->verified = true;
        $promotion->save();

        return response()->json(['message' => 'Promotion for your post has been created.']);
    }

    protected function validatePromotion(PromotionRequest $request): void
    {
        $post = Post::find($request->input('post_id'));
        $city = City::find($request->input('city_id'));

        abort_unless(
            $post->user_id === \Auth::id(),
            Response::HTTP_FORBIDDEN,
            'You are can\'t promote someone else\'s post.'
        );
        abort_if(
            $city && $request->filled('country_id') && $city->country_id !== $request->input('country_id'),
            Response::HTTP_BAD_REQUEST,
            'City must belongs to chosen country.'
        );
    }

    public function show(Promotion $promotion): JsonResponse
    {
        return response()->json(new PromotionResource($promotion));
    }

    public function click(Promotion $promotion): JsonResponse
    {
        abort_if($promotion->views >= $promotion->budget, 400, 'This promotion is inactive.');

        $promotion->increment('clicks');

        return response()->json(['message' => 'Click has been stored.']);
    }

    public function update(PromotionRequest $request, Promotion $promotion): JsonResponse
    {
        $this->validatePromotion($request);

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
}
