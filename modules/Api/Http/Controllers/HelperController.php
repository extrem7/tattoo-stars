<?php

namespace Modules\Api\Http\Controllers;

class HelperController extends Controller
{
    public function root(): array
    {
        return [
            'message' => 'Hello, api user!'
        ];
    }

    /**
     * @api {get} /support Support links
     * @apiName GetSupport
     * @apiGroup General
     *
     * @apiSuccess {String} phone Support phone.
     * @apiSuccess {String} email Support  email.
     * @apiSuccess {Object} messengers Support messengers.
     * @apiSuccess {String} messengers.viber Support viber.
     * @apiSuccess {String} messengers.facebook Support facebook.
     * @apiSuccess {String} messengers.whatsapp Support whatsapp.
     * @apiSuccess {String} messengers.telegram Support telegram.
     *
     */
    public function support()
    {
        return config('api.support');
    }
}
