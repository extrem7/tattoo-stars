<?php

/**
 * @apiDefine User
 * @apiSuccess {Object} user Registered user.
 * @apiSuccess {Number} user.id User id.
 * @apiSuccess {String} user.name User name.
 * @apiSuccess {String} user.email User email.
 */

/**
 * @apiDefine Token
 * @apiHeader  Authorization Bearer token from register|login.
 * @apiHeaderExample {json} Header-Example:
 *     {
 *       "Authorization": "Bearer 21*|1BH99*"
 *     }
 */

namespace Modules\Api\Http\Controllers;

use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Modules\Api\Http\Requests\Users\RegistrationRequest;
use Modules\Api\Http\Resources\UserResource;
use Modules\Api\Notifications\ResetPassword;
use Str;

/**
 * @group User
 */
class UserController extends Controller
{
    /**
     * @api {post} /users/register Register a new user
     * @apiName RegisterUser
     * @apiGroup User
     *
     * @apiParam {String} name User name.
     * @apiParam {String} email User email.
     * @apiParam {String} password User password.
     * @apiParam {File}   [avatar] User avatar file.
     * @apiParam {String} [bio] User biography.
     * @apiParam {String} [country_id] User country id.
     * @apiParam {String} device Device name for token generation.
     *
     * @apiUse User
     * @apiSuccess {String} token Bearer Token.
     */
    public function register(RegistrationRequest $request): array
    {
        $validated = $request->only(['name', 'email']);
        $password = Hash::make($request->input('password'));

        $user = User::create(array_merge($validated, [
            'password' => $password
        ]));

        $user->load('information.country');

        return $this->userWithToken($user, $request->input('device'));
    }

    protected function userWithToken(User $user, string $device = null): array
    {
        $token = $user->createToken($device ?? $user->currentAccessToken()->name);
        return [
            'user' => new UserResource($user),
            'token' => $token->plainTextToken
        ];
    }

    /**
     * @api {post} /users/login Login
     * @apiName LoginUser
     * @apiGroup User
     *
     * @apiParam {String} email User email.
     * @apiParam {String} password User password.
     * @apiParam {String} device Device name for token generation.
     *
     * @apiUse User
     * @apiSuccess {String} token Bearer Token.
     */
    public function login(Request $request): array
    {
        $this->validate($request, [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device' => ['required', 'string']
        ]);

        if (Auth::once($request->only(['email', 'password']))) {
            $user = Auth::getUser();

            return $this->userWithToken($user, $request->input('device'));
        }

        abort(403, trans('auth.failed'));
    }

    /**
     * @api {get} /users/self Get user data
     * @apiName GetSelf
     * @apiGroup User
     *
     * @apiUse Token
     *
     * @apiUse User
     */
    public function self(Request $request): array
    {
        /* @var $user User */
        $user = $request->user();

        return [
            'user' => new UserResource($user)
        ];
    }

    /**
     * @api {post} /users/reset-password Reset passwords
     * @apiName ResetPassword
     * @apiGroup User
     *
     * @apiParam {String} email User email.
     */
    public function resetPassword(Request $request): array
    {
        $this->validate($request, [
            'email' => ['required', 'email']
        ]);

        $user = User::where('email', '=', $request->input('email'))->first();
        if ($user !== null) {
            $password = Str::random(8);
            if ($user->update(['password' => Hash::make($password)])) {
                $user->notify(new ResetPassword($password));
            }
        }
        return ['message' => 'New password has been sent to your email'];
    }
}
