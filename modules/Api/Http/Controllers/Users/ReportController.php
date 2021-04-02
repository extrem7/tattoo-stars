<?php

namespace Modules\Api\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Requests\ReportRequest;
use Modules\Api\Http\Resources\SubscriberResource;
use Modules\Api\Mail\ReportMail;

class ReportController extends Controller
{
    /**
     * @api {post} /users/:id/blacklist Toggle blocking
     * @apiName ToggleBlocking
     * @apiGroup Users
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message Toggle status.
     */
    public function __invoke(User $user, ReportRequest $request): JsonResponse
    {
        $reporter = \Auth::user();

        abort_if($user->id === $reporter->id, 403, 'You cannot report about self.');

        \Mail::to(config('api.support.email'))->send(
            new ReportMail($reporter, $user, $request->input('reason'))
        );

        return response()->json([
            'message' => 'Your report has been sent.'
        ], 201);
    }
}
