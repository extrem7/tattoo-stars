<?php

namespace Modules\Api\Http\Controllers\Profile;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Controllers\Controller;
use Modules\Api\Http\Requests\Users\ProfileUpdateRequest;
use Modules\Api\Repositories\ProfileRepository;

/**
 * @group User
 */
final class ProfileController extends Controller
{
    protected ProfileRepository $repository;

    public function __construct(ProfileRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @api {get} /genders List of genders
     * @apiName GetGenders
     * @apiGroup Profile
     *
     * @apiUse Token
     *
     * @apiSuccess {Number} id Gender id.
     * @apiSuccess {String} name Gender name.
     *
     */
    public function genders(): JsonResponse
    {
        return response()->json($this->repository->getGenders());
    }

    /**
     * @api {get} /styles List of styles
     * @apiName GetStyles
     * @apiGroup Profile
     *
     * @apiUse Token
     *
     * @apiSuccess {Number} id Style id.
     * @apiSuccess {String} name Style name.
     *
     */
    public function styles(): JsonResponse
    {
        return response()->json($this->repository->getStyles());
    }

    /**
     * @api {post} /profile Update profile
     * @apiName UpdateProfile
     * @apiGroup Profile
     *
     * @apiUse Token
     *
     * @apiParam {String} [nickname] User nickname.
     * @apiParam {String} [name] User name.
     * @apiParam {String} [gender_id] User gender.
     * @apiParam {String} [birthday] User birthday.
     * @apiParam {String} [city_id] User city.
     * @apiParam {String} [address] User address.
     * @apiParam {Array} [styles] User styles array of ids.
     * @apiParam {String} [bio] User bio.
     * @apiParam {String} [phone] User phone.
     * @apiParam {String} [email] User email.
     * @apiParam {String} [password] User password.
     * @apiParam {String} [password_confirmation] Password confirmation.
     * @apiParam {String} [website] User website.
     * @apiParam {String} [instagram] User instagram.
     * @apiParam {String} [facebook] User facebook.
     *
     * @apiUse User
     */
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $user = $request->user();
        $main = array_filter($request->only(['nickname', 'name', 'email']), 'strlen');

        $needConfirmEmail = $request->filled('email') && $main['email'] !== $user->email;
        $needUpdatePassword = $request->filled('password');
        if ($needConfirmEmail) {
            $main['email_verified_at'] = null;
        }
        if ($needUpdatePassword) {
            $main['password'] = \Hash::make($request->input('password'));
        }
        if ($user->update($main)) {
            if ($needConfirmEmail) {
                event(new Registered($user));
            }
            if ($needUpdatePassword) {
                $user->tokens()->delete();
            }
        }

        $styles = $request->input('styles');

        $information = $request->only([
            'gender_id', 'birthday', 'city_id', 'address', 'bio', 'phone', 'website', 'instagram', 'facebook'
        ]);

        $this->repository->update($user, $styles, $information);

        $user->loadInfo();

        return response()->json(
            $needUpdatePassword ? $this->repository->getUserWithToken($user) : $this->repository->getUser($user)
        );
    }

    /**
     * @api {delete} /profile Delete user
     * @apiName DeleteUser
     * @apiGroup Profile
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message Is profile deleted message.
     */
    public function destroy(): JsonResponse
    {
        $this->repository->destroy();

        return response()->json(['message' => 'User profile has been deleted.']);
    }
}
