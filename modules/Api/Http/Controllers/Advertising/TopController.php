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

        if ($user->tops()->active()->exists()) {
            abort(Response::HTTP_CONFLICT, 'You are already have top account.');
        }

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
