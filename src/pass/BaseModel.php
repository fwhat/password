<?php

namespace Dowte\Password\pass;

use Dowte\Password\pass\db\ActiveRecord;

class BaseModel extends ActiveRecord
{
    public function loadData($data)
    {
        foreach ($data as $k => $v) {
            $this->$k = $v;
        }
    }

    public function init()
    {
        self::$_db = new Password::$dbClass(array_merge(Password::$dbConfig,
            ['name' => $this->name(), 'model' => $this]));
    }
}