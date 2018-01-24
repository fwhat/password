<?php
namespace Dowte\Password\pass;

class Password
{
    public static $params = [];

    public static $db;

    public function __construct($options = [])
    {
        foreach ($options as $name => $value) {
            self::$params[$name] = $value;
        }
    }
}