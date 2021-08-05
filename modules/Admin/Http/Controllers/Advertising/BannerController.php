<?php

namespace Modules\Admin\Http\Controllers\Advertising;

use App\Models\Advertising\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Admin\Http\Controllers\Controller;
use Modules\Admin\Http\Requests\IndexRequest;
use Modules\Admin\Repositories\AdvertisingRepository;

class BannerController extends Controller
{
    public function index(IndexRequest $request, AdvertisingRepository $repository)
    {
        $this->seo()->setTitle('Реклама в голосовании' . $this->appendPageToTitle($request));

        $banners = $repository->getBannersForIndex($request->validated());

        $repository->transformBanners($banners);

        return inertia('Advertising/Banners', [
            'resource' => 'banners',
            'data' => $banners,
            'table' => [
                'searchQuery' => $request->input('searchQuery'),
                'sortBy' => $request->input('sortBy'),
                'sortDesc' => $request->input('sortDesc')
            ],
            'totals' => [
                'total' => Banner::count(),
                'active' => Banner::active()->count(),
                'paused' => Banner::whereNotNull('on_pause')->count(),
            ]
        ]);
    }

    public function verify(Banner $banner): RedirectResponse
    {
        $banner->verified = true;
        $banner->save();

        return back()->with([
            'message' => 'Реклама баннера была подтверждена.',
            'type' => 'created'
        ]);
    }

    public function reject(Request $request, Banner $banner): RedirectResponse
    {
        $this->validate($request, ['reject_reason' => ['required', 'string255']]);

        $banner->reject_reason = $request->input('reject_reason');
        $banner->verified = false;
        $banner->save();

        return back()->with([
            'message' => 'Реклама баннера была отвергнута.',
            'type' => 'destroy'
        ]);
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        try {
            $banner->delete();
        } catch (\Exception $e) {
            return back()->with(['error' => 'Возникла ошибка при удалении баннера.']);
        }

        return back()->with(['message' => 'Баннер был удален.', 'type' => 'destroy']);
    }
}
