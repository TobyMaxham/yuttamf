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

    protected $baseDirInfo;

    public $capsule;

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

    public function start()
    {
        $dotenv = new Dotenv($this->basedir());
        $dotenv->load();

        $this->startDB();
    }

    private function startDB()
    {

        $this->capsule = new Capsule;

        $this->capsule->addConnection([
            'driver'    => getenv('DB_CONNECTION'),
            'host'      => getenv('DB_HOST'),
            'database'  => getenv('DB_DATABASE'),
            'username'  => getenv('DB_USERNAME'),
            'password'  => getenv('DB_PASSWORD'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        //$this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();
    }

}