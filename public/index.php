<?php

include_once('../common.php');

include_once('../core.php');

class Main extends Api
{
    public function __construct()
    {
        parent::__construct();
    }

    public function render()
    {
        $route = $this->request->get('c', 'encode');
        $output = $this->request->get('t', 'string');

        if (method_exists($this->controller, $output)) {
            $this->controller($route)->{$output}();
        } else {
            header("HTTP/1.0 404 Not Found");
        }
    }
}

set_error_handler('catch_handler', E_ALL & ~E_NOTICE & ~E_USER_NOTICE);

$app = new Main();

$app->render();