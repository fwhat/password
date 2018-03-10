<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

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