<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\Api\Page;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Admin\Http\Requests\IndexRequest;
use Modules\Admin\Http\Requests\PageRequest;
use Nwidart\Modules\Collection;

class PageController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:pages.create')->only(['create', 'store']);
        $this->middleware('can:pages.edit')->only(['edit', 'update']);
        $this->middleware('can:pages.delete')->only('destroy');
    }

    public function index(IndexRequest $request): Response
    {
        $this->seo()->setTitle('Страницы' . $this->appendPageToTitle($request));

        $pages = Page::select(['id', 'title', 'created_at', 'updated_at'])->paginate(10);
        /* @var $pages Collection */
        $pages->transform(fn(Page $p) => $p->translated());

        return inertia('ApiPages/Index', [
            'resource' => 'pages',
            'data' => $pages,
            'table' => [
                'searchQuery' => $request->input('searchQuery'),
                'sortBy' => $request->input('sortBy'),
                'sortDesc' => $request->input('sortDesc')
            ]
        ]);
    }

    public function create(): Response
    {
        $this->seo()->setTitle('Создать страницу');

        return inertia('ApiPages/Form');
    }

    public function store(PageRequest $request): RedirectResponse
    {
        $page = Page::create($request->only(['title', 'body']));

        return redirect()->route('admin.pages.edit', $page->id)->with([
            'message' => "Страница {$request->name} была создана.",
            'type' => 'created'
        ]);
    }

    public function edit(Page $page): Response
    {
        $this->seo()->setTitle('Редактировать страницу');

        $data = $page->only(['id', 'title', 'body']);

        return inertia('ApiPages/Form', [
            'page' => $data
        ]);
    }

    public function update(PageRequest $request, Page $page): RedirectResponse
    {
        $data = $request->only(['title', 'body']);
        $page->update($data);

        return back()->with(['message' => "Страница {$request->name} был отредактирована."]);
    }

    public function destroy(Page $page): RedirectResponse
    {
        try {
            $page->delete();
        } catch (\Exception $e) {
            return back()->with(['error' => 'Возникла ошибка при удалении страницы.']);
        }

        return back()->with(['message' => 'Страница был удалена.', 'type' => 'destroy']);
    }
}
