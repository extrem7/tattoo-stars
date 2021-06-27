<?php

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Requests\SupportHelpRequest;
use Modules\Api\Mail\ContactForm;

final class HelperController extends Controller
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
    public function help(SupportHelpRequest $request): JsonResponse
    {
        \Mail::to(config('tattoo.mail_system_receivers'))->send(new ContactForm($request->validated()));

        return response()->json(['message' => __('tattoo.support.help_message')]);
    }

    /**
     * @api {get} /informer Informer
     * @apiName GetInformer
     * @apiGroup General
     *
     * @apiUse Token
     * @apiSuccess {String} hasMessages Is user have unread messages.
     * @apiSuccess {String} dontVote Is user don't vote today.
     * @apiSuccess {String} hasNotifications Is user have notifications.
     */
    public function informer(): JsonResponse
    {
        $user = \Auth::user();

        $hasMessages = $user->incomeMessages()->unread()->exists();
        $dontVote = !$user->marks()->daily()->exists();
        $hasNotifications = $user->notifications()->unread()->exists();

        return response()->json([
            'hasUnreadMessages' => $hasMessages,
            'dontVote' => $dontVote,
            'hasNotifications' => $hasNotifications
        ]);
    }
}
