<?php

namespace Modules\Admin\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'admin::app';

    public function version(Request $request): ?string
    {
        if (file_exists($manifest = public_path('/admin/dist/mix-manifest.json'))) {
            return md5_file($manifest);
        }
    }

    public function share(Request $request): array
    {
        $shared = [
            'metaInfo' => fn() => [
                'title' => \SEOMeta::getTitle()
            ],
            'auth' => function () use ($request) {
                if (!\Auth::check()) {
                    return;
                }

                $user = $request->user('web');
                $data = $user->only(['id', 'name', 'email']);

                if ($user->avatarMedia) {
                    $data['avatar'] = $user->icon;
                }

                $data['permissions'] = $user->getAllPermissions()->pluck('name');

                return $data;
            },
            'localization' => [
                'locales' => config('tattoo.languages'),
                'default' => config('app.locale'),
                'fallback' => config('app.fallback_locale')
            ],
            'flash' => [],
            'version' => config('admin.version')
        ];

        if ($request->session()->has('message')) {
            $shared['flash']['message'] = fn() => $request->session()->get('message');
            $shared['flash']['type'] = fn() => $request->session()->get('type') ?? 'success';
        }
        if ($request->session()->has('error')) {
            $shared['flash']['error'] = fn() => $request->session()->get('error');
        }

        return array_merge(parent::share($request), $shared);
    }
}
