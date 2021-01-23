<?php

/**
 * @routeNamespace("Modules\Admin\Http\Controllers")
 * @routePrefix("admin.")
 */

R::middleware('guest')->group(function () {
    R::get('/login', 'AuthController@login')->name('login');
});

R::middleware('auth')->group(function () {
    R::get('', 'DashboardController@page')->name('dashboard');

    R::resource('users', 'UserController');
});
