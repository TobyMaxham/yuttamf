<?php

namespace Yutta\Support;

/**
 * Class Session
 * @package Yutta\Support
 * @author Tobias Maxham <git2016@maxham.de>
 */
class Session
{

    /**
     * @var int
     */
    protected $sessionAge = 1800;

    public function close()
    {
        if ('' !== session_id()) {
            return session_write_close();
        }
        return true;
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     * @throws InvalidArgumentTypeException
     */
    public function write($key, $value)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentTypeException('Session key has to be a string');
        }

        $this->init();
        array_set($_SESSION, $key, $value);
        $this->age();
        return $value;
    }

    /**
     * @return bool
     * @throws SessionDisabledException
     * @throws SessionHttpOnlyCookieException
     * @throws SessionUseOnlyCookiesException
     */
    private function init()
    {
        if (session_status() == PHP_SESSION_DISABLED) {
            throw new SessionDisabledException('The session must be enabled');
        }

        if (session_id() !== '') {
            // prevent session hijacking
            return session_regenerate_id(true);
        }

        // create new session
        $secure = true;
        $httponly = true;

        if (ini_set('session.use_only_cookies', 1) === false) {
            throw new SessionUseOnlyCookiesException();
        }

        if (ini_set('session.cookie_httponly', 1) === false) {
            throw new SessionHttpOnlyCookieException();
        }

        $params = session_get_cookie_params();
        session_set_cookie_params($params['lifetime'],
            $params['path'], $params['domain'],
            $secure, $httponly
        );

        if ($name = env('SESSION_NAME', false)) {
            session_name($name);
        }
        return session_start();
    }

    private function age()
    {
        $last = array_get($_SESSION, 'X_LAST_ACTIVE', false);
        if (false !== $last && (time() - $last > $this->sessionAge)) {
            $this->destroy();
            throw new ExpiredSessionException();
        }
        array_set($_SESSION, 'X_LAST_ACTIVE', time());
    }

    public function destroy()
    {
        if (session_id() === '') {
            return;
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 65000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }

    public function delete($key)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentTypeException('Session key has to be a string');
        }

        $this->init();
        array_forget($_SESSION, $key);
        $this->age();
    }

    /**
     * @param $key
     * @param null $default
     * @return bool
     * @throws InvalidArgumentTypeException
     */
    public function read($key, $default = null)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentTypeException('Session key has to be a string');
        }

        $this->init();
        if (($value = array_get($_SESSION, $key, false))) {
            $this->age();
            return $value;
        }
        return $default;
    }
}

class SessionHandlerException extends \Exception
{
}

class SessionDisabledException extends SessionHandlerException
{
}

class InvalidArgumentTypeException extends SessionHandlerException
{
}

class ExpiredSessionException extends SessionHandlerException
{
}

class SessionUseOnlyCookiesException extends SessionHandlerException
{
}

class SessionHttpOnlyCookieException extends SessionHandlerException
{
}

class SessionCookieSecureException extends SessionHandlerException
{
}