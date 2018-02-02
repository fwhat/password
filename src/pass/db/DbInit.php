<?php

namespace Dowte\Password\pass\db;

use Dowte\Password\pass\exceptions\UserException;

class DbInit implements DbInitInterface
{
    const FILE = 'file';
    const SQLITE = 'sqlite';
    const MYSQL = 'mysql';

    public static $way;

    public static $dbInitClass = 'DbInit';

    public function setWay($way)
    {
        self::$way = $way;
        return $this;
    }

    public static function ways()
    {
        return [self::FILE, self::SQLITE, self::MYSQL];
    }

    public function exec()
    {
        if (in_array(self::$way, [self::FILE, self::SQLITE, self::MYSQL])) {
            /**@var $dbInit DbInitInterface*/
            $dbInit = new (self::$way . '\\' . self::$dbInitClass)();
            return $dbInit->exec();
        } else {
            throw new UserException('Init way is invalid.');
        }
    }
}