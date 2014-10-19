<?php

function rglob($pattern = '*', $flags = 0, $path = '')
{
    $paths = glob($path . '*', GLOB_MARK | GLOB_ONLYDIR | GLOB_NOSORT);
    $files = glob($path . $pattern, $flags);
    foreach ($paths as $path) {
        $files = array_merge($files, rglob($pattern, $flags, $path));
    }
    return $files;
}

function pconcat()
{
    $parts = func_get_args();
    $base = array_shift($parts);
    $base = str_replace('\/', "\x01", $base);
    $base = rtrim($base, '/');
    $paths = array();
    foreach ($parts as $part) {
        $part = str_replace('\/', "\x01", $part);
        $part = trim($part, '/');
        if (strlen($part)) {
            $paths[] = $part;
        }
    }
    $fullpath = join($paths, '/');
    $fullpath = $base . '/' . $fullpath;
    $fullpath = str_replace("\x01", '\/', $fullpath);
    return $fullpath;
}

abstract class Api
{
    protected $api_path = '../api/';

    protected $classes = null;

    protected static $objects = array();

    private $status = false;

    private $full_api_path;

    public function __construct()
    {
        $this->status = true;

        $this->full_api_path = realpath(pconcat($_SERVER['DOCUMENT_ROOT'], $this->api_path));

        if ($this->full_api_path == false){
            echo "api path not found";
            exit;
        }

        if (is_null($this->classes)) {
            $this->classes = array();
            $files = rglob('*.php', 0, $this->full_api_path);

            foreach ($files as $file) {
                $path_parts = pathinfo($file);
                $this->classes[strtolower($path_parts['filename'])] = $file;
            }
        }
    }

    public function __get($name)
    {
        if (isset(self::$objects[$name])) {
            return (self::$objects[$name]);
        }

        if (!array_key_exists($name, $this->classes)) {
            return new stdClass();
        }

        $classFile = $this->classes[$name];

        include_once($classFile);

        $className = ucfirst($name);

        self::$objects[$name] = new $className();

        return self::$objects[$name];
    }

    public function __call($name, $args)
    {
        if (!method_exists($this, $name) && !method_exists(__CLASS__, $name)) {
            $fullname = $name . md5(implode($args));
            if (isset(self::$objects[$fullname])) {
                return (self::$objects[$fullname]);
            }

            if (!array_key_exists($name, $this->classes)) {
                if ($this->status == false) {
                    echo "not found base inheritance";
                    exit;
                }
                echo "method not found";
                exit;
            }

            $classFile = $this->classes[$name];

            include_once($classFile);

            $className = ucfirst($name);
            $class = new ReflectionClass($className);
            self::$objects[$fullname] = $class->newInstanceArgs($args);

            return self::$objects[$fullname];
        }

        if (method_exists($this, $name)) {
            return call_user_func_array(
                array($this, $name),
                $args
            );
        } elseif (method_exists(__CLASS__, $name)) {
            return call_user_func_array(
                array(__CLASS__, $name),
                $args
            );
        }
    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array('Classify::' . $method, $args);
    }
}
