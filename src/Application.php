<?php

namespace Yutta;

use Dotenv\Dotenv;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\HttpFoundation\Request;
use Yutta\Support\Router;
use Yutta\Support\Session;

/**
 * Class Application
 * @package Yutta
 * @author Tobias Maxham <git2016@maxham.de>
 */
class Application extends Container
{

    protected $baseDirInfo;

    protected $appStartet = false;

    protected $packageName = 'tobymaxham/yuttamf';

    public function __construct($baseDir = false)
    {
        static::setInstance($this);
        $this->instance('app', $this);

        if (!$baseDir) {
            $baseDir = $this->resolvePath();
        }
        $this->baseDirInfo = pathinfo($baseDir);
        $this['request'] = Request::createFromGlobals();
        $this['router'] = new Router();


        $this->session = new Session();
        $this->session->start();
    }

    private function resolvePath()
    {
        // check if installed in vendor
        $path = 'vendor' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $this->packageName);
        if (strpos(__DIR__, $path) !== false) {
            return str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '../../../../../../');
        }
        return __DIR__;
    }

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
            static::$instance->start();
        }
        if (!self::$instance->started()) {
            static::$instance->start();
        }

        return static::$instance;
    }

    /**
     * @return bool
     */
    public function started()
    {
        return $this->appStartet;
    }

    public function start()
    {
        $dotenv = new Dotenv($this->basedir());
        $dotenv->load();

        $this->startDB();
        $this->startTemplateEngine();
        $this->appStartet = true;
    }

    public function basedir()
    {
        return isset($this->baseDirInfo['dirname']) ? $this->baseDirInfo['dirname'] : '';
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

    /**
     * @return Router
     */
    public function route()
    {
        return $this->router;
    }

    public function isRequest($path, $method = 'GET')
    {
        if ($path == '') {
            $path = '/';
        }
        return $path == $this->request->getPathInfo() && strtoupper($method) == $this->request->getMethod();
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

    public function view($path, array $attributes = [])
    {
        return $this['template']->render($path, $attributes);
    }
}
