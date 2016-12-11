<?php

namespace Yutta;

use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Class Application
 * @package Yutta
 * @author Tobias Maxham <git2016@maxham.de>
 */
class Application
{

    public $capsule;
    protected $baseDirInfo;

    /**
     * @var \Twig_Environment $twigLoader
     */
    private $twig;

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
        return $this->twigLoader->render($path, $attributes);
    }

    public function start()
    {
        $dotenv = new Dotenv($this->basedir());
        $dotenv->load();

        $this->startDB();
        $this->templateEngine();
    }

    private function startDB()
    {

        $this->capsule = new Capsule;

        $this->capsule->addConnection([
            'driver' => getenv('DB_CONNECTION'),
            'host' => getenv('DB_HOST'),
            'database' => getenv('DB_DATABASE'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]);

        //$this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();
    }

    private function templateEngine()
    {
        $loader = new \Twig_Loader_Filesystem($this->basedir() . '/views');
        $this->twig = new \Twig_Environment($loader, array(
            'cache' => $this->basedir() . '/storage/views',
        ));
    }

}