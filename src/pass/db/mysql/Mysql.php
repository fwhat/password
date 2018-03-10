<?php

namespace Dowte\Password\pass\db\mysql;

class Mysql
{
    private static $con;

    public function __construct()
    {
    }

    /**
     * @return \PDO
     */
    public static function getCon()
    {
        if (self::$con === null) {
            self::$con = new \PDO(Connection::$config['DB_DSN'], Connection::$config['DB_USER'], Connection::$config['DB_PASS']);
            self::$con->setAttribute(\PDO::ATTR_EMULATE_PREPARES, FALSE);
        }
        return self::$con;
    }
}