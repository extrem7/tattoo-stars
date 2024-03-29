<?php

/**
 * @routeNamespace("Modules\Admin\Http\Controllers")
 * @routePrefix("admin.")
 */

use Modules\Admin\Http\Controllers\{Advertising\BannerController,
    Advertising\PromotionController,
    AuthController,
    DashboardController,
    FaqController,
    MediaController,
    PageController,
    StoryController,
    Users\AvatarController,
    Users\ProfileController,
    Users\UserController
};
use Modules\Admin\Http\Middleware\RedirectIfAuthenticated;

Route::middleware(RedirectIfAuthenticated::class)->group(function () {
    Route::prefix('login')->as('login')->group(function () {
        Route::get('', [AuthController::class, 'login']);
        Route::post('', [AuthController::class, 'try'])->name('.try');
    });
});

Route::middleware(['auth', 'can:admin-panel.access'])->group(function () {
    Route::delete('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('', [DashboardController::class, 'page'])->name('dashboard');

    Route::resource('users', UserController::class)
        ->parameter('user', 'id')
        ->except(['show'])
        ->middleware('can:users.index');

    Route::resource('stories', StoryController::class)
        ->parameter('story', 'id')
        ->except(['show', 'edit', 'update'])
        ->middleware('can:stories.index');
    Route::prefix('{story}')->name('stories.')->group(function () {
        Route::post('include', [StoryController::class, 'include'])
            ->name('include')
            ->middleware('can:stories.include');
        Route::post('exclude', [StoryController::class, 'exclude'])
            ->name('exclude')
            ->middleware('can:stories.exclude');
    });

    Route::resource('promotions', PromotionController::class)
        ->except(['show', 'edit', 'update'])
        ->middleware('can:promotions.index');
    Route::prefix('promotions/{promotion}')->name('promotions.')->group(function () {
        Route::post('verify', [PromotionController::class, 'verify'])
            ->name('verify')
            ->middleware('can:promotions.verify');
        Route::post('reject', [PromotionController::class, 'reject'])
            ->name('reject')
            ->middleware('can:promotions.reject');
    });

    Route::resource('banners', BannerController::class)
        ->except(['show', 'edit', 'update'])
        ->middleware('can:banners.index');
    Route::prefix('banners/{banner}')->name('banners.')->group(function () {
        Route::post('verify', [BannerController::class, 'verify'])
            ->name('verify')
            ->middleware('can:banners.verify');
        Route::post('reject', [BannerController::class, 'reject'])
            ->name('reject')
            ->middleware('can:banners.reject');
    });

    Route::prefix('users/{user}/avatar')
        ->as('users.avatar.')
        ->middleware('can:users.edit')
        ->group(function () {
            Route::post('', [AvatarController::class, 'update'])->name('update');
            Route::delete('', [AvatarController::class, 'destroy'])->name('destroy');
        });

    Route::resource('pages', PageController::class)
        ->except(['show', 'trash', 'restore', 'force-delete'])
        ->middleware('can:users.index');

    Route::resource('faqs', FaqController::class)
        ->except(['show', 'trash', 'restore', 'force-delete'])
        ->middleware('can:faqs.index');
    Route::post('faqs/sort', [FaqController::class, 'sort'])->name('faqs.sort');

    Route::prefix('profile')->as('profile.')->group(function () {
        Route::get('', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('', [ProfileController::class, 'update'])->name('update');
        Route::prefix('avatar')->as('.avatar.')->group(function () {
            Route::post('', [ProfileController::class, 'updateAvatar'])->name('update');
            Route::delete('', [ProfileController::class, 'destroyAvatar'])->name('destroy');
        });
    });

    Route::post('media', [MediaController::class, 'upload'])->name('media.upload');
});
