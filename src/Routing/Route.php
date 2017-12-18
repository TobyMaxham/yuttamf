<?php

namespace Yutta\Routing;

use Symfony\Component\Routing\CompiledRoute;
use Symfony\Component\Routing\Route as SymfonyRoute;

/**
 * Class Route
 * @package Yutta\Routing
 * @author Tobias Maxham <git2017@maxham.de>
 */
class Route
{

    protected $action;
    protected $method;
    protected $path;

    /**
     * @var CompiledRoute
     */
    protected $compiled;

    public function __construct($action, $method, $path)
    {
        $this->action = $action;
        $this->method = $method;
        $this->path = $path;
        $this->compiled = with(new SymfonyRoute($path))->compile();
    }

    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return CompiledRoute
     */
    public function getCompiled()
    {
        return $this->compiled;
    }

}