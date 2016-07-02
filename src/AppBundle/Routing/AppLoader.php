<?php

namespace AppBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class AppLoader extends Loader
{
    /**
     * @var array
     */
    private $servers = [];

    /**
     * Add server path
     *
     * @param string $name
     * @param string $path
     *
     * @return AppLoader
     */
    public function addServerPath($name, $path)
    {
        $this->servers[$name] = [$path];

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function load($resource, $type = null)
    {
        $routes = new RouteCollection();

        foreach ($this->servers as $name => $info) {
            list ($path) = $info;

            $route = new Route($path);
            $route->setMethods(['GET', 'POST', 'OPTIONS']);
            $route->setDefault('_controller', 'controller.app:handle');
            $route->setDefault('server', $name);

            $routes->add('app_' . $name, $route);
        }

        return $routes;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($resource, $type = null)
    {
        return $type === 'app';
    }
}
