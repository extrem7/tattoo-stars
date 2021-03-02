<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\Api\Faq;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Admin\Http\Controllers\Traits\SortableController;
use Modules\Admin\Http\Requests\IndexRequest;
use Modules\Admin\Http\Requests\FaqRequest;
use Nwidart\Modules\Collection;

class FaqController extends Controller
{
    use SortableController;

    protected string $modelClass = Faq::class;

    public function __construct()
    {
        $this->middleware('can:faqs.create')->only(['create', 'store']);
        $this->middleware('can:faqs.edit')->only(['edit', 'update']);
        $this->middleware('can:faqs.delete')->only('destroy');
    }

    public function index(IndexRequest $request): Response
    {
        $this->seo()->setTitle('Часто задаваемые вопросы' . $this->appendPageToTitle($request));

        $faqs = Faq::ordered()->get(['id', 'question', 'answer', 'order', 'created_at', 'updated_at']);
        /* @var $faqs Collection */
        $faqs->transform(fn(Faq $p) => $p->translated());

        return inertia('ApiFaqs/Index', [
            'resource' => 'faqs',
            'faqs' => $faqs,
        ]);
    }

    public function create(): Response
    {
        $this->seo()->setTitle('Создать вопрос');

        return inertia('ApiFaqs/Form');
    }

    public function store(FaqRequest $request): RedirectResponse
    {
        $faq = Faq::create($request->only(['question', 'answer']));

        return redirect()->route('admin.faqs.edit', $faq->id)->with([
            'message' => "Вопрос {$faq->id} был создан.",
            'type' => 'created'
        ]);
    }

    public function edit(Faq $faq): Response
    {
        $this->seo()->setTitle('Редактировать вопрос');

        $data = $faq->only(['id', 'question', 'answer']);

        return inertia('ApiFaqs/Form', [
            'faq' => $data
        ]);
    }

    public function update(FaqRequest $request, Faq $faq): RedirectResponse
    {
        $data = $request->only(['question', 'answer']);
        $faq->update($data);

        return back()->with(['message' => "Вопрос {$faq->id} был отредактирован."]);
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        try {
            $faq->delete();
        } catch (\Exception $e) {
            return back()->with(['error' => 'Возникла ошибка при удалении вопроса.']);
        }

        return back()->with(['message' => 'Вопрос был удален.', 'type' => 'destroy']);
    }
}
