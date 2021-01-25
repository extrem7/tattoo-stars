<?php

/**
 * @routeNamespace("Modules\Api\Http\Controllers")
 * @routePrefix("api.")
 */

$versions = ['', 'v1'];

foreach ($versions as $version) {
    Route::prefix($version)->middleware('api')->group(function () {
        Route::get('', 'HelperController@root');

        Route::group(['prefix' => 'users', 'middleware' => ['guest']], function () {
            Route::post('register', 'UserController@register');
            Route::post('login', 'UserController@login');
        });
        Route::post('users/reset-password', 'UserController@resetPassword');

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('users/self', 'UserController@self');
        });
    });
}
