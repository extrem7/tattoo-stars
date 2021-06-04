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
 * @apiDefine UserInformation
 * @apiSuccess {Object} user.
 * @apiSuccess {Object} user.gender User gender.
 * @apiSuccess {Number} user.gender.id Gender id.
 * @apiSuccess {String} user.gender.name Gender name.
 * @apiSuccess {String} user.birthday User birthday.
 * @apiSuccess {Object} user.city User city.
 * @apiSuccess {Number} user.city.id City id.
 * @apiSuccess {String} user.city.name City name.
 * @apiSuccess {String} user.address User address.
 * @apiSuccess {String} user.bio User bio.
 * @apiSuccess {String} user.phone User phone.
 * @apiSuccess {String} user.website User website.
 * @apiSuccess {String} user.instagram User instagram.
 * @apiSuccess {String} user.facebook User facebook.
 * @apiSuccess {Object[]} user.styles User styles.
 * @apiSuccess {Number} user.styles.id Style id.
 * @apiSuccess {String} user.styles.name Style name.
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

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Api\Http\Controllers\Controller;
use Modules\Api\Http\Requests\Users\LoginRequest;
use Modules\Api\Http\Requests\Users\RegistrationRequest;
use Modules\Api\Repositories\ProfileRepository;
use Modules\Api\Services\AuthService;

/**
 * @group User
 */
final class AuthController extends Controller
{
    protected AuthService $service;
    protected ProfileRepository $repository;

    public function __construct(AuthService $service, ProfileRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * @api {get} /account-types List of account types
     * @apiName GetAccountTypes
     * @apiGroup Auth
     *
     * @apiSuccess {Number} id Account type id.
     * @apiSuccess {String} name Account type name.
     *
     */
    public function accountTypes(): JsonResponse
    {
        return response()->json($this->repository->getAccountTypes());
    }

    /**
     * @api {post} /users/register Register a new user
     * @apiName RegisterUser
     * @apiGroup Auth
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
        $user = $this->service->register(
            $request->only(['account_type_id', 'nickname', 'email']), $request->input('password')
        );

        return response()->json($this->repository->getUserWithToken($user, $request->input('device')), Response::HTTP_CREATED);
    }

    /**
     * @api {post} /users/login Login
     * @apiName LoginUser
     * @apiGroup Auth
     *
     * @apiParam {String} email_or_nickname User email or nickname.
     * @apiParam {String} password User password.
     * @apiParam {String} device Device name for token generation.
     *
     * @apiUse User
     * @apiSuccess {String} token Bearer Token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        return response()->json($this->repository->getUserWithToken(\Auth::user(), $request->input('device')));
    }

    /**
     * @api {get} /profile/self Full user data
     * @apiName GetSelf
     * @apiGroup Auth
     *
     * @apiUse Token
     *
     * @apiUse User
     * @apiUse UserInformation
     */
    public function self(Request $request): JsonResponse
    {
        return response()->json($this->repository->getUser($request->user()->loadInfo()->append('avatar')));
    }

    /**
     * @api {post} /users/reset-password Reset passwords
     * @apiName ResetPassword
     * @apiGroup Auth
     *
     * @apiParam {String} email User email.
     */
    public function resetPassword(Request $request): array
    {
        $this->validate($request, ['email' => ['required', 'email', 'exists:users,email']]);

        $this->service->resetPassword($request->input('email'));

        return ['message' => __('auth.password_reset_sent')];
    }
}
