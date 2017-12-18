<?php

namespace Yutta\Support;

use Symfony\Component\Routing\CompiledRoute;
use Symfony\Component\Routing\Route as SymfonyRoute;

/**
 * Class Router
 * @package Yutta\Support
 * @author Tobias Maxham <git2016@maxham.de>
 */
class Router
{

    public $routes = [];

    /**
     * @var CompiledRoute
     */
    protected $compiled;

    public function get($path, $action)
    {
        return $this->addRoute('GET', $path, $action);
    }

    public function addRoute($method, $path, $action)
    {
        if ($path == '') {
            $path = '/';
        }

        // never override route
        if (isset($this->routes[$method][$path])) {
            return $this;
        }
        $this->routes[$method][$path] = $action;

        $this->compiled = with(new SymfonyRoute($path))->compile();

        return $this;
    }

    private function checkMatches($method)
    {
        if (!isset($this->routes[$method])) {
            return false;
        }

        foreach ($this->routes[$method] as $uri => $route) {
            if (preg_match($this->compiled->getRegex(), $uri)) {
                return $route;
            }
        }
        return false;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function invoke()
    {
        $path = app()['request']->getPathinfo();
        $method = app()['request']->getMethod();

        if (!isset($this->routes[$method][$path])) {
            if (!($route = $this->checkMatches($method))) {
                throw new \Exception('not a route');
            }
        } else {
            $route = $this->routes[$method][$path];
        }

        if (is_callable($route)) {
            return $route->__invoke();
        }

        if (is_string($route)) {
            return $this->callControllerAction($route, $method);
        }

        throw new \Exception('not a program');
    }

    private function callControllerAction($route, $_method)
    {
        $route = explode('@', $route);
        $ctr = $route[0];

        if (count($route) == 1) {
            $method = 'run';
        } else {
            $method = $route[1];
        }

        if (!class_exists($ctr)) {
            throw new \Exception('class does not exists ' . $ctr);
        }

        $ctr = new $ctr;
        if (!$ctr instanceof Controller) {
            throw new \Exception('not a controller');
        }

        if (!method_exists($ctr, $method)) {
            throw new \Exception('Invalid method ' . $method);
        }

        return $ctr->{$method}($_method);
    }

}