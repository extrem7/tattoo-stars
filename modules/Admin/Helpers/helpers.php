<?php

use Inertia\Inertia;
use Inertia\Response;

function vue($component = null, $props = []): Response
{
    $instance = Inertia::getFacadeRoot();

    if ($component) {
        $props['metaInfo'] = [
            'title' => SEOMeta::getTitle()
        ];
        return $instance->render($component, $props);
    }

    return $instance;
}
