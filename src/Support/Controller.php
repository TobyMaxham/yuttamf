<?php

namespace Yutta\Support;

/**
 * Class Controller
 * @package Yutta\Support
 * @author Tobias Maxham <git2016@maxham.de>
 */
abstract class Controller
{

    public final function run($method)
    {
        if ($method == 'GET') {
            return $this->index();
        }
    }

}