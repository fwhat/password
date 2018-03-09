<?php

namespace Dowte\Password\pass\db;


class DbException
{
    const DB_BASE_EXCEPTION = 10001;

    public function error($errorMessage, $errorCode = self::DB_BASE_EXCEPTION)
    {
        throw new \Exception($errorMessage, $errorCode);
    }
}