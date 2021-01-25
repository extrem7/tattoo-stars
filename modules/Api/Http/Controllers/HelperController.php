<?php

namespace Modules\Api\Http\Controllers;

class HelperController extends Controller
{
    public function root(): array
    {
        return [
            'message' => 'Hello, api user!'
        ];
    }
}
