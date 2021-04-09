<?php

namespace Modules\Api\Repositories;

use App\Models\Api\Faq;
use App\Models\Api\Page;

class PageRepository
{
    public function getFaq(): array
    {
        $page = Page::find(1);
        $faqs = Faq::ordered()->get(['question', 'answer']);

        return array_merge(
            $page->translated()->only(['title', 'body']),
            ['faqs' => $faqs->map(fn(Faq $faq) => $faq->translated())]
        );
    }

    public function getPrivacyPolicy(): array
    {
        $page = Page::find(2);
        return $page->translated()->only(['title', 'body']);
    }
}
