<?php
namespace Dowte\Password\base;

class Password
{
    public static $params = [];

    public function __construct($options = [])
    {
        foreach ($options as $name => $value) {
            self::$params[$name] = $value;
        }
    }
}