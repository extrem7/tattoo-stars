<?php

/**
 * @apiDefine User
 * @apiSuccess {Object} user Registered user.
 * @apiSuccess {Number} user.id User id.
 * @apiSuccess {String} user.nickname User nickname.
 * @apiSuccess {String} user.name User name.
 * @apiSuccess {String} user.email User email.
 * @apiSuccess {Boolean} user.emailVerified Is user email verified.
 * @apiSuccess {String} user.avatar User avatar.
 * @apiSuccess {Object} user.accountType User account type.
 * @apiSuccess {Number} user.accountType.id Account type id.
 * @apiSuccess {String} user.accountType.name Account type name.
 */

/**
 * @apiDefine Token
 * @apiHeader  Authorization Bearer token from register|login.
 * @apiHeaderExample {json} Header-Example:
 *     {
 *       "Authorization": "Bearer 21*|1BH99*"
 *     }
 */

namespace Modules\Api\Http\Controllers\Auth;

use App\Models\User;
use App\Models\User\AccountType;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Api\Http\Controllers\Controller;
use Modules\Api\Http\Requests\Users\LoginRequest;
use Modules\Api\Http\Requests\Users\RegistrationRequest;
use Modules\Api\Notifications\ResetPassword;

/**
 * @group User
 */
class AuthController extends Controller
{
    /**
     * @api {get} /account-types List of account types
     * @apiName GetAccountTypes
     * @apiGroup Auth
     *
     * @apiSuccess {Number} id Account type id.
     * @apiSuccess {String} name Account type name.
     *
     */
    public function accountTypes(): array
    {
        $accountTypes = AccountType::all(['id', 'name']);

        return array_reverse($accountTypes->toArray());
    }

    /**
     * @api {post} /users/register Register a new user
     * @apiName RegisterUser
     * @apiGroup User
     *
     * @apiParam {Number} account_type_id User account type.
     * @apiParam {String} nickname User nickname.
     * @apiParam {String} email User email.
     * @apiParam {String} password User password.
     * @apiParam {String} password_confirmation Password confirmation.
     * @apiParam {String} device Device unique name for token generation.
     *
     * @apiUse User
     * @apiSuccess {String} token Bearer Token.
     */
    public function register(RegistrationRequest $request): JsonResponse
    {
        $validated = $request->only(['account_type_id', 'nickname', 'email']);
        $password = \Hash::make($request->input('password'));

        $user = User::create(array_merge($validated, [
            'name' => $request->nickname,
            'password' => $password
        ]));

        event(new Registered($user));

        return response()->json($this->getUserWithToken($user, $request->device), 201);
    }

    /**
     * @api {post} /users/login Login
     * @apiName LoginUser
     * @apiGroup User
     *
     * @apiParam {String} email_or_nickname User email or nickname.
     * @apiParam {String} password User password.
     * @apiParam {String} device Device name for token generation.
     *
     * @apiUse User
     * @apiSuccess {String} token Bearer Token.
     */
    public function login(LoginRequest $request): array
    {
        $request->authenticate();

        return $this->getUserWithToken(\Auth::getUser(), $request->device);
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
        return $this->getUser($request->user()->loadInfo()->append('avatar'));
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
            'email' => ['required', 'email', 'exists:users,email']
        ]);

        if ($user = User::where('email', '=', $request->email)->first()) {
            $password = \Str::random(8);
            if ($user->update(['password' => \Hash::make($password)])) {
                $user->notify(new ResetPassword($password));
                $user->tokens()->delete();
            }
        }

        return ['message' => __('auth.password_reset_sent')];
    }
}
