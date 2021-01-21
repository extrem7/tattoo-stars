<?php

namespace Modules\Admin\Http\Controllers;

use Inertia\Response;

class AuthController extends Controller
{
    public function login(): Response
    {
        $this->seo()->setTitle('Вход');

        return vue('Login');
    }
}
