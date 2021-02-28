<?php

namespace Modules\Api\Http\Controllers;

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
        return response()->json([
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
        return response()->json([
            'view' => $this->viewController->privacyPolicy()->render()
        ]);
    }
}
