<?php

namespace Dowte\Password\pass\db\sqlite;


class Sqlite extends \SQLite3
{
    public static $file = __DIR__ . '/../../../../data/pass.db';

    /**
     * @var \SQLite3
     */
    public static $db;

    public static $dbKey;

    public function __construct()
    {

    }

    public function init()
    {
        if (file_exists(self::$file)) {
            self::$db = new \SQLite3(self::$file, SQLITE3_OPEN_READWRITE, self::$dbKey);

        } else {
            $fp = fopen(self::$file, 'w+');
            fclose($fp);
            self::$db = new \SQLite3(self::$file, SQLITE3_OPEN_READWRITE, self::$dbKey);
        }

    }
}