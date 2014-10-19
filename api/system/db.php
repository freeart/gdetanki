<?php

class Db extends Api
{
    private $link;

    public $mapper = array(
        'default' => array(
            'db' => 'test',
            'user' => 'postgres',
            'pwd' => '123456'
        )
    );

    public function __construct($name = 'default')
    {
        parent::__construct();

        $this->connect($this->mapper[$name]['db'], $this->mapper[$name]['user'], $this->mapper[$name]['pwd']);
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