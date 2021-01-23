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
            'flash' => []
        ];

        if ($request->session()->has('message')) {
            $shared['flash']['message'] = fn() => $request->session()->get('message');
            $shared['flash']['type'] = fn() => $request->session()->get('type');
        }
        if ($request->session()->has('error')) {
            $shared['flash']['error'] = fn() => $request->session()->get('error');
        }

        return array_merge(parent::share($request), $shared);
    }
}
