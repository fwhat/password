<?php

namespace Dowte\Password\pass\db\sqlite;


use Dowte\Password\commands\InitCommand;
use Dowte\Password\pass\exceptions\QueryException;
use Dowte\Password\pass\SymfonyApplication;

class Sqlite extends \SQLite3
{
    /**
     * @var \SQLite3
     */
    public static $db;

    protected static $_dbKey;

    public function __construct()
    {
    }

    public function init()
    {
        if (file_exists(SQLITE_FILE)) {
            self::$db = new \SQLite3(SQLITE_FILE, SQLITE3_OPEN_READWRITE, self::$_dbKey);

        } else {
            throw new QueryException('The db file is not exists, please exec init at first');
        }
    }
}