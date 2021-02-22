<?php

use Modules\Api\Http\Controllers\{CitiesController,
    HelperController,
    Profile\AvatarController,
    Profile\ProfileController,
    Auth\AuthController,
    Auth\EmailVerificationController
};

$versions = ['', 'v1'];

foreach ($versions as $version) {
    Route::prefix($version)->middleware('api')->group(function () {
        Route::get('', [HelperController::class, 'root']);

        Route::get('support', [HelperController::class, 'support']);
        Route::post('support/help', [HelperController::class, 'help'])->middleware('throttle:1,1');

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

                Route::prefix('profile')->group(function () {
                    Route::post('', [ProfileController::class, 'update']);
                    Route::prefix('avatar')->group(function () {
                        Route::post('', [AvatarController::class, 'store']);
                        Route::delete('', [AvatarController::class, 'destroy']);
                    });
                });
            });
        });
    });
}
