<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\Story;
use Illuminate\Http\RedirectResponse;
use Modules\Admin\Http\Requests\IndexRequest;
use Modules\Admin\Repositories\StoryRepository;

final class StoryController extends Controller
{
    protected StoryRepository $repository;

    public function __construct(StoryRepository $repository)
    {
        $this->repository = $repository;

        $this->middleware('can:stories.delete')->only('destroy');
    }

    public function index(IndexRequest $request)
    {
        $title = 'Stories';
        if ($page = $request->input('page')) {
            $title .= " - страница $page";
        }
        $this->seo()->setTitle($title);

        $stories = $this->repository->getForIndex($request->only(['searchQuery', 'sortBy', 'sortDesc']));
        $this->repository->transformStories($stories);

        return inertia('Stories', [
            'resource' => 'stories',
            'data' => $stories,
            'table' => [
                'searchQuery' => $request->input('searchQuery'),
                'sortBy' => $request->input('sortBy'),
                'sortDesc' => $request->input('sortDesc')
            ]
        ]);
    }

    public function destroy(Story $story): RedirectResponse
    {
        $story->delete();

        return back()->with(['message' => 'Story была удалена.', 'type' => 'destroy']);
    }

    public function include(Story $story): RedirectResponse
    {
        $story->included = !$story->included ? true : null;
        if ($story->included) {
            $story->excluded = null;
        }
        $story->save();

        return back();
    }

    public function exclude(Story $story): RedirectResponse
    {
        $story->excluded = !$story->excluded ? true : null;
        if ($story->excluded) {
            $story->included = null;
        }
        $story->save();

        return back();
    }
}
