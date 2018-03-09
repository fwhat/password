<?php

namespace Dowte\Password\pass\db\yamlFile;

use Dowte\Password\pass\db\BaseConnection;

class Connection extends BaseConnection
{
    protected function setActiveRecordClass()
    {
        $this->_activeRecordClass = YamlActiveRecord::class;
    }

    protected function allowProperties()
    {
        return ['dbDir'];
    }

    protected function setActiveQueryClass()
    {
        $this->_activeQueryClass = YamlQuery::class;
    }

    protected function requireProperties()
    {
        return ['dbDir'];
    }
}