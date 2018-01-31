<?php

namespace Dowte\Password\pass;


use Dowte\Password\pass\db\sqlite\Sqlite;

class PassInit
{
    public function InitSqlite($dbKey = null)
    {
        $sqlite = new Sqlite();
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
}