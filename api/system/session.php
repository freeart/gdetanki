<?php

class Session extends Api
{
    public function __construct()
    {
	    parent::__construct();

        $cookieLifetime = 365 * 24 * 60 * 60; // A year in seconds
        @session_set_cookie_params($cookieLifetime);
        @session_cache_limiter('nocache');
        @session_start();
    }

    public function exists($name){
        if (!isset($_SESSION[$name])) {
            return false;
        }
        return true;
    }

    public function get($name)
    {
        if (!isset($_SESSION[$name])) {
            return null;
        }
        return $_SESSION[$name];
    }

    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    public function remove($name)
    {
        if (isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
        }
    }

}
