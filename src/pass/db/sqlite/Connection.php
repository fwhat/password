<?php

namespace Dowte\Password\pass\db\sqlite;

use Dowte\Password\pass\db\BaseConnection;

class Connection extends BaseConnection
{
    protected function setActiveRecordClass()
    {
        $this->_activeRecordClass = SqliteActiveRecord::class;
    }

    protected function allowProperty()
    {
        return ['dbDir'];
    }

    protected function setActiveQueryClass()
    {
        $this->_activeQueryClass = SqliteQuery::class;
    }
}