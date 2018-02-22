<?php

namespace Dowte\Password\pass\db;

use Dowte\Password\pass\Password;

class DbInit implements DbInitInterface
{
    const FILE = 'file';
    const SQLITE = 'sqlite';
    const MYSQL = 'mysql';
    const DB_NAMESPACE = 'Dowte\Password\pass\db\\';

    protected static $_way;

    protected static $_dbInitClassName = 'DbInit';

    public function setWay($way)
    {
        self::$_way = $way;
        return $this;
    }

    public static function ways()
    {
//        return [self::FILE, self::SQLITE, self::MYSQL];
        return [self::SQLITE];
    }

    public function exec()
    {
        if (in_array(self::$_way, self::ways())) {
            $dbClass = self::DB_NAMESPACE . self::$_way . '\\' . self::$_dbInitClassName;
            /**@var $dbInit DbInitInterface*/
            $dbInit = new $dbClass();
            return $dbInit->exec();
        } else {
            Password::error('Init way is invalid.');
        }
    }
}