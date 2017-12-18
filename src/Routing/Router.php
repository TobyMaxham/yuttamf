<?php

namespace Yutta\Routing;
use Yutta\Support\Controller;

/**
 * Class Router
 * @package Yutta\Routing
 * @author Tobias Maxham <git2016@maxham.de>
 */
class Router
{

    public $routes = [];

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
        $this->routes[$method][$path] = with(new Route($action, $method, $path));

        return $this;
    }

    private function checkMatches($method)
    {
        if (!isset($this->routes[$method])) {
            return false;
        }

        /** @var Route $route **/
        foreach ($this->routes[$method] as $uri => $route) {
            if (preg_match($route->getCompiled()->getRegex(), $uri)) {
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

        $routeAction = $route->getAction();

        if (is_callable($routeAction)) {
            return $routeAction->__invoke();
        }

        if (is_string($routeAction)) {
            return $this->callControllerAction($route);
        }

        throw new \Exception('not a program');
    }

    /**
     * @param Route $_route
     * @return mixed
     * @throws \Exception
     */
    private function callControllerAction($_route)
    {
        $routeAction = $_route->getAction();

        $route = explode('@', $routeAction);
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

        return $ctr->{$method}($_route->getMethod());
    }

}