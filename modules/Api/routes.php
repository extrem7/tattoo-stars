<?php

use Modules\Api\Http\Controllers\{Auth\AuthController,
    Auth\EmailVerificationController,
    ChatController,
    CitiesController,
    CommentController,
    HelperController,
    NotificationController,
    PageController,
    PageViewController,
    Posts\BookmarkController,
    Posts\PostController,
    Profile\AvatarController,
    Profile\ProfileController,
    StoryController,
    Users\BlacklistController,
    Users\ReportController,
    Users\SubscriptionController,
    Users\UserController
};
use Modules\Api\Http\Middleware\IsChatParticipant;
use Modules\Api\Http\Middleware\NotBlocked;

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

            Route::middleware('verified')->group(function () {
                Route::get('genders', [ProfileController::class, 'genders']);
                Route::get('styles', [ProfileController::class, 'styles']);

                Route::get('countries/{query?}', [CitiesController::class, 'countries']);
                Route::get('cities/{country}/{query?}', [CitiesController::class, 'cities']);
                Route::get('geoip', [CitiesController::class, 'geoip']);

                Route::get('blacklist', [BlacklistController::class, 'blacklist']);
                Route::get('bookmarks', [BookmarkController::class, 'index']);

                Route::prefix('profile')->group(function () {
                    Route::get('self', [AuthController::class, 'self']);
                    Route::post('', [ProfileController::class, 'update']);
                    Route::delete('', [ProfileController::class, 'destroy']);

                    Route::prefix('avatar')->group(function () {
                        Route::post('', [AvatarController::class, 'store']);
                        Route::delete('', [AvatarController::class, 'destroy']);
                    });
                });

                Route::prefix('posts')->group(function () {
                    Route::get('', [PostController::class, 'index']);
                    Route::get('search', [PostController::class, 'search']);
                    Route::post('create', [PostController::class, 'store']);

                    Route::prefix('{post}')->middleware(NotBlocked::class)->group(function () {
                        Route::get('', [PostController::class, 'show']);
                        Route::delete('', [PostController::class, 'destroy']);

                        Route::post('like', [PostController::class, 'like']);
                        Route::post('bookmark', [BookmarkController::class, 'toggle']);

                        Route::prefix('comments')->group(function () {
                            Route::get('{comment?}', [CommentController::class, 'index']);
                            Route::post('{parent?}', [CommentController::class, 'store']);
                            Route::delete('{comment}', [CommentController::class, 'destroy']);
                        });
                    });
                });

                Route::prefix('users')->group(function () {
                    Route::get('', [UserController::class, 'index']);
                    Route::get('self', [UserController::class, 'self']);

                    Route::prefix('{user}')->middleware(NotBlocked::class)->group(function () {
                        Route::get('', [UserController::class, 'show']);

                        Route::get('subscribers', [SubscriptionController::class, 'subscribers']);
                        Route::get('subscriptions', [SubscriptionController::class, 'subscriptions']);
                        Route::post('subscribe', [SubscriptionController::class, 'toggle']);

                        Route::post('blacklist', [BlacklistController::class, 'toggle']);
                        Route::post('report', ReportController::class)->middleware('throttle:1,360');

                        Route::post('chat', [ChatController::class, 'start']);
                    });
                });

                Route::prefix('stories')->group(function () {
                    Route::get('', [StoryController::class, 'index']);
                    Route::post('{post}', [StoryController::class, 'store']);
                    Route::prefix('{story}')->group(function () {
                        Route::post('view', [StoryController::class, 'view']);
                        Route::post('like', [StoryController::class, 'like']);
                        Route::post('dislike', [StoryController::class, 'dislike']);
                    });
                });

                Route::prefix('chats')->group(function () {
                    Route::get('', [ChatController::class, 'index']);
                    Route::prefix('{chat}')->middleware(IsChatParticipant::class)->group(function () {
                        Route::get('', [ChatController::class, 'show']);
                        Route::post('', [ChatController::class, 'storeMessage']);
                        Route::post('mark', [ChatController::class, 'toggleMark']);
                        Route::delete('', [ChatController::class, 'destroy']);
                    });
                });

                Route::prefix('notifications')->group(function () {
                    Route::get('', [NotificationController::class, 'index']);
                });
            });
        });
    });
}

Route::prefix('preview')->group(function () {
    Route::get('reference', [PageViewController::class, 'reference']);
    Route::get('privacy-policy', [PageViewController::class, 'privacyPolicy']);
});

