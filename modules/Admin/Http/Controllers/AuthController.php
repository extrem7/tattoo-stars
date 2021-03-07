<?php

namespace Modules\Admin\Http\Controllers;

use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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

        if (!$request->user()->can('admin-panel.access')) {
            \Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'У вас нету доступа к админ-панели.',
            ]);
        }

        $request->session()->regenerate();

        return inertia()->location('/');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
