<?php

namespace Modules\Api\Http\Controllers;

use Illuminate\Contracts\View\View;
use Modules\Api\Repositories\PageRepository;

class PageViewController extends Controller
{
    protected PageRepository $repository;

    public function __construct()
    {
        $this->repository = app(PageRepository::class);
    }

    public function reference(): View
    {
        return view('api::reference', $this->repository->getFaq());
    }

    public function privacyPolicy(): View
    {
        return view('api::page', $this->repository->getPrivacyPolicy());
    }
}
