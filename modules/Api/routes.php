<?php

/**
 * @routeNamespace("Modules\Api\Http\Controllers")
 * @routePrefix("api.")
 */

$versions = ['', 'v1'];

foreach ($versions as $version) {
    Route::prefix($version)->middleware('api')->group(function () {
        Route::get('', 'HelperController@root');
        Route::get('account-types', 'AuthController@accountTypes');

        Route::prefix('users')
            ->namespace('Auth')
            ->middleware('guest')
            ->group(function () {
                Route::post('register', 'AuthController@register');
                Route::post('login', 'AuthController@login');
                Route::post('reset-password', 'AuthController@resetPassword')
                    ->middleware('throttle:1,1');
            });

        Route::middleware('auth:sanctum')->group(function () {
            Route::prefix('verify-email')->namespace('Auth')->group(function () {
                Route::post('', 'EmailVerificationController@verify');
                Route::get('resend', 'EmailVerificationController@resend')
                    ->middleware('throttle:1,1');
            });

            Route::get('users/self', 'Auth\AuthController@self');
        });
    });
}
