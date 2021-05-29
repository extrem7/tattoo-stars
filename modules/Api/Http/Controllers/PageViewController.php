<?php

namespace Modules\Api\Http\Controllers;

use Illuminate\Contracts\View\View;
use Modules\Api\Repositories\PageRepository;

final class PageViewController extends Controller
{
    protected PageRepository $repository;

    public function __construct(PageRepository $repository)
    {
        $this->repository = $repository;
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
