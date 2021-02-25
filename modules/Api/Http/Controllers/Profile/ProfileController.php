<?php

namespace Modules\Api\Http\Controllers\Profile;

use App\Models\User\Gender;
use App\Models\User\Style;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Controllers\Controller;
use Modules\Api\Http\Requests\Users\ProfileUpdateRequest;

/**
 * @group User
 */
class ProfileController extends Controller
{
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
        $genders = Gender::all();

        return response()->json($genders->toArray());
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
        $styles = Style::all();

        return response()->json($styles->toArray());
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

        if ($request->filled('styles') && $styles = $request->input('styles')) {
            $user->styles()->sync($styles);
        }

        $additional = $request->only([
            'gender_id', 'birthday', 'city_id', 'address', 'bio', 'phone', 'website', 'instagram', 'facebook'
        ]);

        $user->information->update($additional);

        $user->loadInfo();

        return response()->json($needUpdatePassword ? $this->getUserWithToken($user) : $this->getUser($user));
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
        $user = \Auth::user();
        $user->update(['deleted_self' => true]);
        $user->delete();
        //todo soft delete relations

        return response()->json(['message' => 'User profile has been deleted.']);
    }
}
