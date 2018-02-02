<?php

namespace Dowte\Password\pass\db\sqlite;

use Dowte\Password\pass\db\DbClearInterface;

class DbClear implements DbClearInterface
{
    public function exec()
    {
        $sql = '';
        foreach (DbInit::getTables() as $table) {
            $sql .= "DROP TABLE {$table};";
        }
        Sqlite::$db->exec($sql);
    }
}