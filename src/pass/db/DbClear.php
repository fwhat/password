<?php

namespace Dowte\Password\pass\db;

class DbClear implements DbClearInterface
{
    public function exec()
    {
        (new \Dowte\Password\pass\db\sqlite\DbClear())->exec();
    }
}