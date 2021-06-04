<?php

namespace Modules\Api\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Modules\Api\Http\Controllers\Controller;

final class EmailVerificationController extends Controller
{
    /**
     * @api {post} /verify-email Verify email
     * @apiName VerifyEmail
     * @apiGroup Auth
     *
     * @apiUse Token
     *
     * @apiParam {Number} code Verification code.
     *
     * @apiSuccess {String} message.
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'numeric']
        ]);

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            abort(Response::HTTP_FORBIDDEN, 'Already verified.');
        }

        if ($user->email_verification_code === $request->code) {
            $user->markEmailAsVerified();
            return response()->json([
                'message' => __('auth.email_verified')
            ]);
        }

        throw ValidationException::withMessages([
            'code' => __('auth.failed_email_verification'),
        ]);
    }

    /**
     * @api {get} /verify-email/resend Resend email verification
     * @apiName VerifyEmailResend
     * @apiGroup Auth
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message.
     */
    public function resend(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            return response()->json(['message' => __('auth.email_verification_resent')]);
        }

        abort(Response::HTTP_FORBIDDEN, 'Already verified.');
    }
}
