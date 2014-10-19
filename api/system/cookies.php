<?php

class Cookies extends Api
{

	public function __construct()
	{
		parent::__construct();
	}

	public function get($name)
	{
		if (!isset($_COOKIE[$name])) {
			return null;
		}
		return $_COOKIE[$name];
	}

	public function remove($name)
	{
		setcookie($name, false, time() - 60 * 100000, '/', 'www.placeinspain.es');
	}

	public function set($name, $value, $days = 365)
	{
		$timeout = time() + ($days * 24 * 60 * 60);

		setcookie($name, $value, $timeout, '/', 'www.placeinspain.es');
	}
}
