<?php

namespace Dowte\Password\pass\db\sqlite;

use Dowte\Password\pass\db\ConnectionInterface;
use Dowte\Password\pass\Password;

class Connection implements ConnectionInterface
{
    public static function init($config)
    {
        Password::$dbClass = SqliteActiveRecord::class;
        Password::$dbConfig = $config;
    }
}