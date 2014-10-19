<?php

class Controller extends Api
{
    private $route;

    public function __construct($route = null)
    {
        parent::__construct();

        $this->route = $route;
    }

    public function access()
    {
        $method = $this->request->get('m', 'string');

        if (method_exists($this->{$this->route}, $method)) {
            $this->{$this->route}->{$method}();
        } else {
            header("HTTP/1.0 404 Not Found");
            exit;
        }
    }

    public function tpl()
    {
        $this->template->assign('controller', $this->route);
        $this->template->assign('this', $this);

        header("content-type: text/html; charset=utf-8");

        if (is_dir($this->template->getTemplateDir()[0] . $this->route)) {
            $this->template->fetch('functions.tpl');
            echo $this->template->fetch('layout.tpl');
        } else {
            header("HTTP/1.0 404 Not Found");
            exit;
        }
    }

    public function json()
    {
        $method = $this->request->get('m', 'string');

        if (method_exists($this->{$this->route}, $method)) {
            header("content-type: application/json; charset=utf-8");
            echo json_encode($this->{$this->route}->{$method}());
        } else {
            header("HTTP/1.0 404 Not Found");
            exit;
        }
    }

    public function html()
    {
        $route = str_replace(array('.', '/'), "", $this->route);
        $file = str_replace(array('.', '/'), "", $this->request->get('f', 'system'));

        $route = str_replace('~', '/', $route);


        $this->template->assign('controller', $route);
        $this->template->assign('this', $this);

        header("content-type: text/html; charset=utf-8");

        if (is_dir($this->template->getTemplateDir()[0] . $route)) {
            if (defined('DEV') && isMobile() && file_exists($this->template->getTemplateDir()[0] . $route . '/' . $file . '.mobile.dev.tpl')) {
                $tpl = $route . '/' . $file . '.mobile.dev.tpl';
            }
            if (isMobile() && file_exists($this->template->getTemplateDir()[0] . $route . '/' . $file . '.mobile.tpl')) {
                $tpl = $route . '/' . $file . '.mobile.tpl';
            }
            if (defined('DEV') && empty($tpl) && file_exists($this->template->getTemplateDir()[0] . $route . '/' . $file . '.dev.tpl')) {
                $tpl = $route . '/' . $file . '.dev.tpl';
            }
            if (empty($tpl) && file_exists($this->template->getTemplateDir()[0] . $route . '/' . $file . '.tpl')) {
                $tpl = $route . '/' . $file . '.tpl';
            }

            if (!empty($tpl)) {
                $this->template->fetch('functions.tpl');
                echo $this->template->fetch($tpl);
            } else {
                echo '';
            }
        } else {
            echo '';
        }
    }
}
