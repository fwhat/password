<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

namespace Dowte\Password\pass\db;


class RegisterDb
{
    public function __construct()
    {
        DbHelper::$exception = new DbException();
    }

    public function registerDbException(DbException $dbException)
    {
        DbHelper::$exception = $dbException;
    }
}