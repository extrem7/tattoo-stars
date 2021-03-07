<?php

namespace Modules\Api\Http\Controllers;

use App\Models\Api\Page;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{
    protected PageViewController $viewController;

    public function __construct()
    {
        $this->viewController = app(PageViewController::class);
    }

    /**
     * @api {get} /reference Reference information
     * @apiName GetReference
     * @apiGroup General
     *
     * @apiSuccess {String} view Page html.
     */
    public function reference(): JsonResponse
    {
        $page = Page::find(1);

        return response()->json([
            'title' => $page->translated()->title,
            'view' => $this->viewController->reference()->render()
        ]);
    }

    /**
     * @api {get} /privacy-policy Privacy policy
     * @apiName GetPrivacyPolicy
     * @apiGroup General
     *
     * @apiSuccess {String} view Page html.
     */
    public function privacyPolicy(): JsonResponse
    {
        $page = Page::find(2);

        return response()->json([
            'title' => $page->translated()->title,
            'view' => $this->viewController->privacyPolicy()->render()
        ]);
    }
}
