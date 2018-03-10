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
}