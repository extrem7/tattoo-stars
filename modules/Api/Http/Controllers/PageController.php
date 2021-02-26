<?php

namespace Modules\Api\Http\Controllers;

use App\Models\Api\Faq;
use App\Models\Api\Page;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{
    /**
     * @api {get} /reference Reference information
     * @apiName GetReference
     * @apiGroup General
     *
     * @apiSuccess {String} title Page title.
     * @apiSuccess {String} body Page html body.
     */
    public function reference(): JsonResponse
    {
        $page = Page::find(1);
        $faqs = Faq::ordered()->get(['question', 'answer']);

        return response()->json(array_merge(
            $page->translated()->only(['title', 'body']),
            ['faqs' => $faqs->map(fn(Faq $faq) => $faq->translated())]
        ));
    }

    /**
     * @api {get} /privacy-policy Privacy policy
     * @apiName GetPrivacyPolicy
     * @apiGroup General
     *
     * @apiSuccess {String} title Page title.
     * @apiSuccess {String} body Page html body.
     */
    public function privacyPolicy(): JsonResponse
    {
        $page = Page::find(2);

        return response()->json($page->translated()->only(['title', 'body']));
    }
}
