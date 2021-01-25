<?php

/**
 * @routeNamespace("Modules\Admin\Http\Controllers")
 * @routePrefix("admin.")
 */

Route::middleware('guest')->group(function () {
    Route::prefix('login')->as('login')->group(function () {
        Route::get('', 'AuthController@login');
        Route::post('', 'AuthController@try')->name('.try');
    });
});

Route::middleware('auth')->group(function () {
    Route::delete('logout', 'AuthController@logout')->name('logout');

    Route::get('', 'DashboardController@page')->name('dashboard');

    Route::namespace('Users')->group(function () {
        Route::resource('users', 'UserController');
        Route::prefix('users/{user}/avatar')->as('users.avatar.')->group(function () {
            Route::post('', 'AvatarController@update')->name('update');
            Route::delete('', 'AvatarController@destroy')->name('destroy');
        });
    });

});
