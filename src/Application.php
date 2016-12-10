<?php

namespace Yutta;

/**
 * Class Application
 * @package Yutta
 * @author Tobias Maxham <git2016@maxham.de>
 */
class Application
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

}