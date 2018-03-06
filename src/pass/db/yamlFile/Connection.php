<?php

namespace Dowte\Password\pass\db\yamlFile;

use Dowte\Password\pass\db\ConnectionInterface;
use Dowte\Password\pass\Password;

class Connection implements ConnectionInterface
{
    public static function init($config)
    {
        Password::$dbClass = YamlActiveRecord::class;
        Password::$dbConfig = $config;
    }
}