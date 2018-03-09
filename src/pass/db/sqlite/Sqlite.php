<?php

namespace Dowte\Password\pass\db\sqlite;


use Dowte\Password\pass\exceptions\BaseException;
use Dowte\Password\pass\Password;

class Sqlite extends \SQLite3
{
    /**
     * @var \SQLite3
     */
    private static $db;

    public function __construct()
    {
    }

    public static function getFromFile($tableName)
    {
        return $tableName . '.db';
    }

    public static function getDbResource($dbDir, $from)
    {
        return rtrim($dbDir, '/') . '/' . self::getFromFile($from);
    }

    public static function getDb()
    {
        if (self::$db === null) {
            $resource = self::getDbResource(Connection::$config['dbDir'], Connection::$config['dbName']);
            if (file_exists($resource)) {
                self::$db = new \SQLite3($resource, SQLITE3_OPEN_READWRITE, Connection::$config['dbKey']);

            } else {
                Password::error('The db file is not exists, please exec init at first.', BaseException::QUERY_CODE);
            }
        }
        return self::$db;
    }
}