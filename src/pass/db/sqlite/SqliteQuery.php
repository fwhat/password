<?php

namespace Dowte\Password\pass\db\sqlite;


use Dowte\Password\pass\db\ActiveQuery;

class SqliteQuery extends ActiveQuery
{
    public function one()
    {
        return SqliteActiveRecord::findOne();
    }

    public function all()
    {
        return SqliteActiveRecord::findAll();
    }
}