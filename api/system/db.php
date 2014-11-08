<?php

require_once '../../dev/pg_types/Abstract/Base.php';
require_once '../../dev/pg_types/Abstract/Primitive.php';
require_once '../../dev/pg_types/Abstract/Container.php';
require_once '../../dev/pg_types/Abstract/Wrapper.php';
require_once '../../dev/pg_types/Pgsql/Hstore.php';
require_once '../../dev/pg_types/Pgsql/HstoreRow.php';
require_once '../../dev/pg_types/Pgsql/Array.php';
require_once '../../dev/pg_types/Pgsql/Row.php';
require_once '../../dev/pg_types/String.php';
require_once '../../dev/pg_types/Wrapper/NullToDefault.php';
require_once '../../dev/pg_types/Exception/Common.php';

class Db extends Api
{
	private $link;

	public $mapper = array(
		'default' => array(
			'db' => 'gdetanki',
			'user' => 'gdetanki',
			'pwd' => 'root'
		)
	);

	public function __construct($name = 'default')
	{
		parent::__construct();

		try {
			$this->connect($this->mapper[$name]['db'], $this->mapper[$name]['user'], $this->mapper[$name]['pwd']);
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function __destruct()
	{
		$this->disconnect();
	}

	private function connect($db, $user, $pwd)
	{
		if (!empty($this->link))
			return $this->link;

		try {
			$this->link = new PDO("pgsql:dbname=$db;host=localhost", "$user", "$pwd");
		} catch (PDOException $e) {
			echo $e->getMessage();
		}

		return $this->link;
	}

	private function disconnect()
	{
		$dbh = null;
		return true;
	}

	public static function __callStatic($method, $args)
	{
		return call_user_func_array('PDO::' . $method, $args);
	}

	public function __call($method, $args)
	{
		return call_user_func_array(array($this->link, $method), $args);
	}
}