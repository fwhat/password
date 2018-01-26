<?php

namespace Dowte\Password\pass\db\file;

use Dowte\Password\pass\db\ActiveQuery;

class FileQuery extends ActiveQuery
{

    public function one()
    {
        return FileActiveRecord::findOne();
    }

    public function all()
    {
        return FileActiveRecord::findAll();
    }
}