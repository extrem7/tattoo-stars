<?php

namespace Modules\Api\Http\Controllers\Advertising;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Modules\Api\Http\Requests\Advertising\TopRequest;

class TopController extends Controller
{
    public function status(): JsonResponse
    {
        $top = \Auth::user()->tops()->active()->first();

        $response = [
            'status' => $top !== null
        ];

        if ($top) {
            $response['until'] = $top->end_at->format('d.m.Y');
        }

        return response()->json($response);
    }

    public function pin(TopRequest $request): JsonResponse
    {
        $user = \Auth::user();

        abort_unless(
            in_array($user->account_type_id, [3, 4], true),
            Response::HTTP_FORBIDDEN,
            'Pin to top is now allowed for your account type.'
        );

        abort_if(
            $user->tops()->active()->exists(), Response::HTTP_CONFLICT, 'You are already have top account.'
        );

        $days = $request->input('days');

        $top = $user->tops()->create(['days' => $days]);

        // todo billing stuff
        $top->start_at = now();
        $top->end_at = now()->addDays($days);
        $top->save();

        return response()->json([
            'message' => 'Your account has been pinned to top.'
        ]);
    }
}
