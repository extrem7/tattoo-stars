<?php

namespace Modules\Admin\Http\Controllers\Advertising;

use App\Models\Advertising\Promotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Admin\Http\Controllers\Controller;
use Modules\Admin\Http\Requests\IndexRequest;
use Modules\Admin\Repositories\AdvertisingRepository;

class PromotionController extends Controller
{
    public function index(IndexRequest $request, AdvertisingRepository $repository)
    {
        $this->seo()->setTitle('Реклама публикаций' . $this->appendPageToTitle($request));

        $promotions = $repository->getPromotionsForIndex($request->validated());

        $repository->transformPromotions($promotions);

        return inertia('Advertising/Promotions', [
            'resource' => 'promotions',
            'data' => $promotions,
            'table' => [
                'searchQuery' => $request->input('searchQuery'),
                'sortBy' => $request->input('sortBy'),
                'sortDesc' => $request->input('sortDesc')
            ],
            'totals' => [
                'total' => Promotion::count(),
                'active' => Promotion::active()->count(),
                'paused' => Promotion::whereNotNull('on_pause')->count(),
            ]
        ]);
    }

    public function verify(Promotion $promotion): RedirectResponse
    {
        $promotion->verified = true;
        $promotion->reject_reason = null;
        $promotion->save();

        return back()->with([
            'message' => 'Реклама была подтверждена.',
            'type' => 'created'
        ]);
    }

    public function reject(Request $request, Promotion $promotion): RedirectResponse
    {
        $this->validate($request, ['reject_reason' => ['required', 'string255']]);

        $promotion->reject_reason = $request->input('reject_reason');
        $promotion->verified = false;
        $promotion->save();

        return back()->with([
            'message' => 'Реклама была отвергнута.',
            'type' => 'destroy'
        ]);
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        try {
            $promotion->delete();
        } catch (\Exception $e) {
            return back()->with(['error' => 'Возникла ошибка при удалении рекламы.']);
        }

        return back()->with(['message' => 'Реклама был удалена.', 'type' => 'destroy']);
    }
}
