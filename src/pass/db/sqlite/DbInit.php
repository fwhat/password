<?php

namespace Dowte\Password\pass\db\sqlite;


use Dowte\Password\pass\db\DbInitInterface;

class DbInit implements DbInitInterface
{
    public function exec()
    {
        $sqlite = new Sqlite();
        if (! file_exists(SQLITE_FILE)) {
            $fp = fopen(SQLITE_FILE, 'w+');
            fclose($fp);
        }
//        $sqlite::$dbKey = $dbKey;
        $sqlite->init();
        $sql = <<<EOF
CREATE TABLE IF NOT EXISTS user (
                    id INTEGER PRIMARY KEY, 
                    username VARCHAR(255) NOT NULL, 
                    password VARCHAR(255) NOT NULL)
EOF;

        $sqlite::$db->exec($sql);
        $sql = <<<EOF
CREATE TABLE IF NOT EXISTS password (
                    id INTEGER PRIMARY KEY, 
                    user_id INTEGER NOT NULL, 
                    name VARCHAR(255) NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    description VARCHAR(255) NOT NULL,
                    FOREIGN KEY(user_id) REFERENCES user(id)
                    )
EOF;
        $sqlite::$db->exec($sql);
    }

    public static function getTables()
    {
        return ['user', 'password'];
    }
}