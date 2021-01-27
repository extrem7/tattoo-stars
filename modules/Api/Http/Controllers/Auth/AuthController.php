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

namespace Modules\Api\Http\Controllers\Auth;

use App\Models\User;
use App\Models\User\AccountType;
use Auth;
use Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Api\Http\Controllers\Controller;
use Modules\Api\Http\Requests\Users\LoginRequest;
use Modules\Api\Http\Requests\Users\RegistrationRequest;
use Modules\Api\Http\Resources\UserResource;
use Modules\Api\Notifications\ResetPassword;
use Str;

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
     * @apiSuccess {String} name Account type name (system name).
     * @apiSuccess {String} label Account type label.
     *
     */
    public function accountTypes(): array
    {
        $accountTypes = AccountType::all(['id', 'name'])
            ->map(fn(AccountType $t) => $t->append('label'));

        return array_reverse($accountTypes->toArray());
    }

    /**
     * @api {post} /users/register Register a new user
     * @apiName RegisterUser
     * @apiGroup User
     *
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
        $password = Hash::make($request->input('password'));

        $user = User::create(array_merge($validated, [
            'name' => $request->nickname,
            'password' => $password
        ]));

        event(new Registered($user));

        return response()->json($this->userWithToken($user, $request->device), 201);
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
    public function login(LoginRequest $request): array
    {
        $request->authenticate();

        return $this->userWithToken(Auth::getUser(), $request->device);
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
            'email' => ['required', 'email', 'exists:users,email']
        ]);

        if ($user = User::where('email', '=', $request->email)->first()) {
            $password = Str::random(8);
            if ($user->update(['password' => Hash::make($password)])) {
                $user->notify(new ResetPassword($password));
                $user->tokens()->delete();
            }
        }

        return ['message' => __('auth.password_reset_sent')];
    }

    protected function userWithToken(User $user, string $device = null): array
    {
        $token = $user->createToken($device ?? $user->currentAccessToken()->name);
        return [
            'user' => new UserResource($user),
            'token' => $token->plainTextToken
        ];
    }
}
