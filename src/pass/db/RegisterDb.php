<?php

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