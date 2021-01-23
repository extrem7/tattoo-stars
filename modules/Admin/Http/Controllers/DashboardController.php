<?php

namespace Modules\Admin\Http\Controllers;

use Inertia\Response;

class DashboardController extends Controller
{
    public function page(): Response
    {
        $this->seo()->setTitle('Главная');

        return inertia('Dashboard');
    }
}
