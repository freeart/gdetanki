<?php

class User extends Api
{
    private $name;

    public function __construct($name = 'default')
    {
        parent::__construct();

        $this->name = $name;
    }

    public function getName()
    {
        return 'User name is ' . $this->name;
    }
}
