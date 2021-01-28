<?php

use Modules\Api\Http\Controllers\{
    HelperController,
    Auth\AuthController,
    Auth\EmailVerificationController,
};

$versions = ['', 'v1'];

foreach ($versions as $version) {
    Route::prefix($version)->middleware('api')->group(function () {
        Route::get('', [HelperController::class, 'root']);
        Route::get('support', [HelperController::class, 'support']);
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
        });
    });
}
