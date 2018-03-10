<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

namespace Dowte\Password\pass\db;


class DbException
{
    const DB_BASE_EXCEPTION = 10001;

    public function error($errorMessage, $errorCode = self::DB_BASE_EXCEPTION)
    {
        throw new \Exception($errorMessage, $errorCode);
    }
}