<?php

namespace Dowte\Password\pass\exceptions;


use Dowte\Password\pass\Password;

class DbException extends \Dowte\Password\pass\db\DbException
{
    public function error($errorMessage, $errorCode = \Dowte\Password\pass\db\DbException::DB_BASE_EXCEPTION)
    {
        Password::error($errorMessage, $errorCode);
    }
}