<?php

namespace Yutta;

use Dotenv\Dotenv;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Class Application
 * @package Yutta
 * @author Tobias Maxham <git2016@maxham.de>
 */
class Application extends Container
{

    protected $baseDirInfo;

    public function __construct($baseDir)
    {
        $this->baseDirInfo = pathinfo($baseDir);
    }

    public function publicPath($path = '')
    {
        $aPath = $this->basedir() . DIRECTORY_SEPARATOR . 'public';

        if (trim($path) == '') {
            return $aPath;
        }

        if (substr($path, 0, 1) == '/' || substr($path, 0, 1) == DIRECTORY_SEPARATOR) {
            return $aPath . $path;
        }
        return $aPath . DIRECTORY_SEPARATOR . $path;
    }

    public function basedir()
    {
        return isset($this->baseDirInfo['dirname']) ? $this->baseDirInfo['dirname'] : '';
    }

    public function view($path, array $attributes = [])
    {
        return $this['template']->render($path, $attributes);
    }

    public function start()
    {
        $dotenv = new Dotenv($this->basedir());
        $dotenv->load();

        $this->startDB();
        $this->startTemplateEngine();
    }

    private function startDB()
    {
        $this['db'] = new Capsule;
        $this['db']->addConnection([
            'driver' => env('DB_CONNECTION'),
            'host' => env('DB_HOST'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => env('DB_PREFIX', ''),
        ]);

        $this['db']->bootEloquent();
    }

    private function startTemplateEngine()
    {
        $loader = new \Twig_Loader_Filesystem($this->basedir() . '/views');
        $this['template'] = new \Twig_Environment($loader, [
            'cache' => env('APP_ENV') == 'prod' ? $this->basedir() . '/storage/views' : false,
        ]);
    }

}