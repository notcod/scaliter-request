<?php

namespace Scaliter;

use Scaliter\Response as Response;

class Request
{

    public static $query, $request, $cookie, $file, $server, $env;
    public static array $db;
    public static $url, $json, $root;
    public $value, $key;

    public function __construct($value, $key)
    {
        $this->value = $value;
        $this->key = $key;
        return $this;
    }
    public function fn($functions)
    {
        $value = $this->value;
        $fu = explode(',', str_replace(' ', '', $functions));
        foreach ($fu as $fn)
            $value = $fn($value);
        return new self($value, $this->key);
    }
    public function error($response, $content = [], $code = 202)
    {
        if (empty($this->value) || !strlen($this->value) || $this->value == 0 || $this->value == null)
            throw new Response($response, $content, $code);
        return $this;
    }
    public function value($default){
        if($this->value == "")
            return $default;
        return $this->value;
    }
    public static function initialize(array $query = [], array $request = [], array $cookie = [], array $file = [], array $server = [])
    {
        self::$query = $query;
        self::$request = $request;
        self::$cookie = $cookie;
        self::$file = $file;
        self::$server = $server;

        self::$url = self::url();
        self::$root = $server['DOCUMENT_ROOT'] ?? '/';
    }
    public static function get($key)
    {
        $value = self::$query[$key] ?? '';
        return new self($value, $key);
    }
    public static function post($key)
    {
        $value = htmlspecialchars(trim(self::$request[$key] ?? ''));
        return new self($value, $key);
    }
    public static function cookie($key)
    {
        $value = self::$cookie[$key] ?? '';
        return new self($value, $key);
    }
    public static function file($key)
    {
        $value = self::$file[$key] ?? '';
        return new self($value, $key);
    }
    public static function server($key)
    {
        $value = self::$server[$key] ?? '';
        return new self($value, $key);
    }
    public static function env($key, $value = null)
    {
        $value = self::$server[$key] ?? '';
        return new self($value, $key);
    }
    public static function url()
    {
        $HTTPS = self::server('HTTPS')->value;
        $HTTPS = $HTTPS == 'off' || $HTTPS == '' ? 'http' : 'https';
        $SERVER_NAME = self::server('SERVER_NAME')->value;

        return empty($SERVER_NAME) ? '' : $HTTPS . '://' . $SERVER_NAME . '/';
    }
    public function __toString()
    {
        return $this->value;
    }
}