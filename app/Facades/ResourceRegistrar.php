<?php

namespace App\Facades;

use Illuminate\Routing\ResourceRegistrar as BaseResourceRegistrar;
use Illuminate\Routing\Route;

class ResourceRegistrar extends BaseResourceRegistrar
{
    /* The verbs used in the resource URIs.
    *
    * @var array
         */
    protected static $verbs = [
        'create' => 'create',
        'edit' => 'edit',
        'trash' => 'trash',
        'restore' => 'restore',
        'forceDestroy' => 'force-destroy',
    ];
    /* The default actions for a resourceful controller.
     *
     * @var string[]
     */
    protected $resourceDefaults = [
        'index', 'create', 'store', 'show', 'edit', 'update', 'destroy', 'trash', 'restore', 'forceDestroy'
    ];

    /* Add the index method for a resourceful route.
     *
     * @param  string  $name
     * @param  string  $base
     * @param  string  $controller
     * @param  array  $options
     * @return \Illuminate\Routing\Route
     */
    protected function addResourceTrash($name, $base, $controller, $options): Route
    {
        $uri = $this->getResourceUri($name) . '/' . static::$verbs['trash'];

        $action = $this->getResourceAction($name, $controller, 'trash', $options);

        return $this->router->get($uri, $action);
    }

    /* Add the edit method for a resourceful route.
     *
     * @param  string  $name
     * @param  string  $base
     * @param  string  $controller
     * @param  array  $options
     * @return \Illuminate\Routing\Route
     */
    protected function addResourceRestore($name, $base, $controller, $options): Route
    {
        $name = $this->getShallowName($name, $options);

        $uri = $this->getResourceUri($name) . '/{' . $base . '}/' . static::$verbs['restore'];

        $action = $this->getResourceAction($name, $controller, 'restore', $options);

        return $this->router->post($uri, $action);
    }

    /**
     * Add the edit method for a resourceful route.
     *
     * @param string $name
     * @param string $base
     * @param string $controller
     * @param array $options
     * @return Route
     */
    protected function addResourceForceDestroy($name, $base, $controller, $options): Route
    {
        $name = $this->getShallowName($name, $options);

        $uri = $this->getResourceUri($name) . '/{' . $base . '}/' . static::$verbs['forceDestroy'];

        $action = $this->getResourceAction($name, $controller, 'forceDestroy', $options);

        return $this->router->delete($uri, $action);
    }
}
