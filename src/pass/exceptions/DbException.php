<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

namespace Dowte\Password\pass\exceptions;


use Dowte\Password\pass\Password;

class DbException extends \Dowte\Password\pass\db\DbException
{
    public function error($errorMessage, $errorCode = \Dowte\Password\pass\db\DbException::DB_BASE_EXCEPTION)
    {
        Password::error($errorMessage, $errorCode);
    }
}