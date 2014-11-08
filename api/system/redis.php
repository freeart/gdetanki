<?php

require_once '../lib/vendor/composer/autoload_real.php';

class Redis extends Api
{
	private $link;

	public function __construct($name = 'default')
	{
		parent::__construct();

		 ComposerAutoloaderInit17b7214ec51501e2e1aa152e657679c0::getLoader();

		$this->link = $redis = new redisent\Redis('redis://localhost/');
	}

	public function __call($method, $args)
    {
        return call_user_func_array(array($this->link, $method), $args);
    }
}