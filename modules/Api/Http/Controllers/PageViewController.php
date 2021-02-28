<?php

namespace Modules\Api\Http\Controllers;

use App\Models\Api\Faq;
use App\Models\Api\Page;
use Illuminate\Contracts\View\View;

class PageViewController extends Controller
{
    public function reference(): View
    {
        $page = Page::find(1);
        $faqs = Faq::ordered()->get(['question', 'answer']);

        return view('api::reference', array_merge(
            $page->translated()->only(['title', 'body']),
            ['faqs' => $faqs->map(fn(Faq $faq) => $faq->translated())]
        ));
    }

    public function privacyPolicy(): View
    {
        $page = Page::find(2);

        return view('api::page', $page->translated()->only(['title', 'body']));
    }
}
