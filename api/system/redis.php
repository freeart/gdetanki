<?php

require_once '../lib/vendor/composer/autoload_real.php';

class Redis extends Api
{
	private $link;

	public function __construct($name = 'default')
	{
		parent::__construct();

		ComposerAutoloaderInit7414297074fd4b16ee1fbef0c2897395::getLoader();

		$this->link = $redis = new redisent\Redis('redis://localhost/');
	}

	public function __call($method, $args)
    {
        return call_user_func_array(array($this->link, $method), $args);
    }
}