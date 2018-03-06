<?php

namespace Dowte\Password\pass\db\yamlFile;


use Dowte\Password\pass\db\ActiveQuery;

class YamlQuery extends ActiveQuery
{
    public function one()
    {
        return YamlActiveRecord::findOne();
    }

    public function all()
    {
        return YamlActiveRecord::findAll();
    }
}