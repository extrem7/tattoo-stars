<?php

namespace Modules\Api\Http\Controllers;

use Modules\Api\Http\Requests\SupportHelpRequest;
use Modules\Api\Mail\ContactForm;

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

    /**
     * @api {post} /support/help Ask for help
     * @apiName GetSupportHelp
     * @apiGroup General
     *
     * @apiParam {String} name Email.
     * @apiParam {String} email Email.
     * @apiParam {String} message Message.
     *
     * @apiSuccess {String} message Response text.
     *
     */
    public function help(SupportHelpRequest $request)
    {
        \Mail::to(config('api.support.email'))->send(new ContactForm($request->validated()));

        return response()->json(['message' => 'Your message has been sent.']);
    }
}
