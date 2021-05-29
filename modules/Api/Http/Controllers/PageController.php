<?php

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\JsonResponse;

final class PageController extends Controller
{
    protected PageViewController $viewController;

    public function __construct(PageViewController $viewController)
    {
        $this->viewController = $viewController;
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
        $page = $this->viewController->reference();

        return response()->json([
            'title' => $page->getData()['title'],
            'view' => $page->render()
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
        $page = $this->viewController->privacyPolicy();

        return response()->json([
            'title' => $page->getData()['title'],
            'view' => $page->render()
        ]);
    }
}
