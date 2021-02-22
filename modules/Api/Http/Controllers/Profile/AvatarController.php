<?php

namespace Modules\Api\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Requests\Users\AvatarRequest;

class AvatarController extends Controller
{
    /**
     * @api {post} /profile/avatar Upload user's avatar
     * @apiName UploadAvatar
     * @apiGroup Profile
     *
     * @apiUse Token
     *
     * @apiParam {File} avatar User avatar in format:jpg,jpeg,bmp,png; and smaller than 4096kb.
     *
     * @apiSuccess {String} message Uploading status message.
     * @apiSuccess {String} avatar User avatar or placeholder url.
     */
    public function store(AvatarRequest $request): JsonResponse
    {
        $uploaded = null;

        try {
            $uploaded = $request->uploadAvatar();
        } catch (\Exception $e) {
            \Log::error('Error during avatar uploading: ' . $e->getMessage());
        }

        return response()->json([
            'message' => $uploaded ? 'Avatar has been uploaded' : 'Error during uploading',
            'avatar' => $uploaded ?? $request->user()->avatar
        ]);
    }

    /**
     * @api {delete} /profile/avatar Delete user's avatar
     * @apiName DeleteAvatar
     * @apiGroup Profile
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message Uploading status message.
     */
    public function destroy(): JsonResponse
    {
        if (($user = \Auth::user()) && $user->avatarMedia) {
            $user->avatarMedia->delete();
        }
        return response()->json([
            'message' => 'Avatar has been deleted.',
        ]);
    }
}
