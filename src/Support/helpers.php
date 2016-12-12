<?php

if (!function_exists('env')) {

    /**
     * @param $key
     * @param null $default
     * @return bool|null|string
     */
    function env($key, $default = null)
    {
        $value = getenv($key);
        if (false === $value) {
            return $default;
        }

        switch ($value) {
            case 'true':
            case 'TRUE':
                return true;
            case 'false':
            case 'FALSE':
                return false;
            case 'null':
            case 'NULL':
                return null;
        }

        return $value;
    }
}