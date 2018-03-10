<?php

namespace Dowte\Password\pass\db\mysql;


use Dowte\Password\pass\db\BaseConnection;

class Connection extends BaseConnection
{
    protected function setActiveRecordClass()
    {
        $this->_activeRecordClass = MysqlActiveRecord::class;
    }

    protected function allowProperties()
    {
        return ['DB_DSN', 'DB_USER', 'DB_PASS'];
    }

    public static function requireProperties()
    {
        return ['DB_DSN', 'DB_USER', 'DB_PASS'];
    }

    protected function setActiveQueryClass()
    {
        $this->_activeQueryClass = MysqlQuery::class;
    }
}