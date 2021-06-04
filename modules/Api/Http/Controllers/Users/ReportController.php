<?php

namespace Modules\Api\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Modules\Api\Http\Requests\ReportRequest;
use Modules\Api\Services\UserService;

final class ReportController extends Controller
{
    /**
     * @api {post} /users/:id/report Send report
     * @apiName SendReport
     * @apiGroup Users
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message Report status.
     */
    public function __invoke(User $user, ReportRequest $request, UserService $service): JsonResponse
    {
        $reporter = \Auth::user();

        $service->sendReport($reporter, $user, $request->input('reason'));

        return response()->json([
            'message' => __('tattoo.report.sent')
        ], Response::HTTP_CREATED);
    }
}
