<?php

namespace Dowte\Password\pass\db\file;

use Dowte\Password\pass\db\ConnectionInterface;
use Dowte\Password\pass\Password;

class Connection implements ConnectionInterface
{
    public static function init($config)
    {
        Password::$dbClass = FileActiveRecord::class;
        Password::$dbConfig = $config;
    }
}