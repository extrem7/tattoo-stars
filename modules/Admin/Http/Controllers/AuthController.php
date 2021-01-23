<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Modules\Admin\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    public function login(): Response
    {
        $this->seo()->setTitle('Вход');

        return inertia('Login');
    }

    public function try(LoginRequest $request): \Illuminate\Http\Response
    {
        $request->authenticate();

        $request->session()->regenerate();

        return response()->noContent();
    }

    public function logout(Request $request): RedirectResponse
    {
        \Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
