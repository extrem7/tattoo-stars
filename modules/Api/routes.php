<?php

use Modules\Api\Http\Controllers\{CitiesController,
    HelperController,
    PageController,
    PageViewController,
    PostController,
    Profile\AvatarController,
    Profile\ProfileController,
    Auth\AuthController,
    Auth\EmailVerificationController,
    Users\BlacklistController,
    Users\SubscriptionController,
    Users\UserController
};

$versions = ['', 'v1'];

foreach ($versions as $version) {
    Route::prefix("$version/")->middleware('api')->group(function () {
        Route::get('', [HelperController::class, 'root']);

        Route::get('support', [HelperController::class, 'support']);
        Route::post('support/help', [HelperController::class, 'help'])->middleware('throttle:1,1');

        Route::get('reference', [PageController::class, 'reference']);
        Route::get('privacy-policy', [PageController::class, 'privacyPolicy']);

        Route::get('account-types', [AuthController::class, 'accountTypes']);

        Route::prefix('users')
            ->middleware('guest')
            ->group(function () {
                Route::post('register', [AuthController::class, 'register']);
                Route::post('login', [AuthController::class, 'login']);
                Route::post('reset-password', [AuthController::class, 'resetPassword'])
                    ->middleware('throttle:1,1');
            });

        Route::middleware('auth:sanctum')->group(function () {
            Route::prefix('verify-email')->group(function () {
                Route::post('', [EmailVerificationController::class, 'verify']);
                Route::get('resend', [EmailVerificationController::class, 'resend'])
                    ->middleware('throttle:1,1');
            });

            Route::get('users/self', [AuthController::class, 'self']);

            Route::middleware('verified')->group(function () {
                Route::get('genders', [ProfileController::class, 'genders']);
                Route::get('styles', [ProfileController::class, 'styles']);

                Route::get('countries/{query?}', [CitiesController::class, 'countries']);
                Route::get('cities/{country}/{query?}', [CitiesController::class, 'cities']);
                Route::get('geoip', [CitiesController::class, 'geoip']);

                Route::get('blacklist', [BlacklistController::class, 'blacklist']);

                Route::prefix('profile')->group(function () {
                    Route::post('', [ProfileController::class, 'update']);
                    Route::delete('', [ProfileController::class, 'destroy']);
                    Route::prefix('avatar')->group(function () {
                        Route::post('', [AvatarController::class, 'store']);
                        Route::delete('', [AvatarController::class, 'destroy']);
                    });
                });

                Route::prefix('posts')->group(function () {
                    Route::post('create', [PostController::class, 'store']);
                    Route::delete('{post}', [PostController::class, 'destroy']);
                });

                Route::prefix('users')->group(function () {
                    Route::get('self', [UserController::class, 'self']);

                    Route::prefix('{user}')->group(function () {
                        Route::get('', [UserController::class, 'show']);

                        Route::get('subscribers', [SubscriptionController::class, 'subscribers']);
                        Route::get('subscriptions', [SubscriptionController::class, 'subscriptions']);
                        Route::post('subscribe', [SubscriptionController::class, 'toggle']);

                        Route::post('blacklist', [BlacklistController::class, 'toggle']);
                    });
                });
            });
        });
    });
}

Route::prefix('preview')->group(function () {
    Route::get('reference', [PageViewController::class, 'reference']);
    Route::get('privacy-policy', [PageViewController::class, 'privacyPolicy']);
});

