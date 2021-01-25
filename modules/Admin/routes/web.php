<?php

/**
 * @routeNamespace("Modules\Admin\Http\Controllers")
 * @routePrefix("admin.")
 */

use Modules\Admin\Http\Middleware\RedirectIfAuthenticated;

Route::middleware(RedirectIfAuthenticated::class)->group(function () {
    Route::prefix('login')->as('login')->group(function () {
        Route::get('', 'AuthController@login');
        Route::post('', 'AuthController@try')->name('.try');
    });
});

Route::middleware(['auth', 'can:admin-panel.access'])->group(function () {
    Route::delete('logout', 'AuthController@logout')->name('logout');

    Route::get('', 'DashboardController@page')->name('dashboard');

    Route::namespace('Users')->group(function () {
        Route::resource('users', 'UserController')
            ->except('show')
            ->middleware('can:users.index');
        Route::prefix('users/{user}/avatar')
            ->as('users.avatar.')
            ->middleware('can:users.edit')
            ->group(function () {
                Route::post('', 'AvatarController@update')->name('update');
                Route::delete('', 'AvatarController@destroy')->name('destroy');
            });

        Route::prefix('profile')->as('profile.')->group(function () {
            Route::get('', 'ProfileController@edit')->name('edit');
            Route::patch('', 'ProfileController@update')->name('update');
            Route::prefix('avatar')->as('.avatar.')->group(function () {
                Route::post('', 'ProfileController@updateAvatar')->name('update');
                Route::delete('', 'ProfileController@destroyAvatar')->name('destroy');
            });
        });
    });

});
