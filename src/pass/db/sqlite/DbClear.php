<?php

namespace Dowte\Password\pass\db\sqlite;

use Dowte\Password\pass\db\DbClearInterface;
use Dowte\Password\pass\Password;

class DbClear implements DbClearInterface
{
    public function exec()
    {
        $sql = '';
        foreach (DbInit::getTables() as $table) {
            $sql .= sprintf("DELETE FROM %s WHERE username = '%s';\n", $table, Password::getUser());
        }
        Sqlite::$db->exec($sql);
    }
}