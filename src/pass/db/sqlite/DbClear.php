<?php

namespace Dowte\Password\pass\db\sqlite;

use Dowte\Password\forms\PasswordForm;
use Dowte\Password\forms\UserForm;
use Dowte\Password\pass\db\DbClearInterface;
use Dowte\Password\pass\Password;

class DbClear implements DbClearInterface
{
    public function exec()
    {
        $sql = '';
        $user = UserForm::user()->findOne(['username' => Password::getUser()], ['id']);
        if (! $user) {
            return;
        }
        $userId = $user['id'];
        $sql .= sprintf("DELETE FROM password WHERE user_id = %d;\n", $userId);
        $sql .= sprintf("DELETE FROM user WHERE id = %d;\n", $userId);
        Sqlite::$db->exec($sql);
    }
}