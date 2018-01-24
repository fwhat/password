<?php

namespace Dowte\Password\pass\db;

use Dowte\Password\pass\Password;

class ActiveRecord implements ActiveRecordInterface
{
    protected static $db;

    public function attributeLabels()
    {
    }

    public function name()
    {
    }

    public function rules()
    {
    }

    /**
     * @return ActiveRecordInterface
     */
    public static function getDb()
    {
        return Password::$db;
    }

    /**
     * @return ActiveQuery
     */
    public static function find()
    {
        return (self::getDb())::find();
    }

    /**
     * @return integer
     */
    public function save()
    {
        // TODO: Implement save() method.
    }
}