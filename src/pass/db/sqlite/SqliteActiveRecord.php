<?php

namespace Dowte\Password\pass\db\sqlite;


use Dowte\Password\pass\db\ActiveQuery;
use Dowte\Password\pass\db\ActiveRecordInterface;
use Dowte\Password\pass\db\BaseActiveRecordInterface;

class SqliteActiveRecord extends Sqlite implements BaseActiveRecordInterface
{
    /**
     * @var ActiveQuery
     */
    protected static $_query;

    /**
     * @var ActiveRecordInterface
     */
    public static $modelClass;

    /**
     * @var array
     */
    protected static $_data;

    /**
     * SqliteActiveRecord constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        foreach ($config as $k => $value) {
            ! property_exists(__CLASS__, $k) or static::$$k = $value;
        }
    }

    public function save()
    {
        $header = 'INSERT INTO ' . self::$modelClass->name() . '(';
        $values = 'VALUES(';
        foreach (self::$modelClass->attributeLabels() as $k => $v) {
            if ($k === 'id') continue;
            $header .= '`' . trim($k) . '`,';
            $values .= '\''. trim(self::$modelClass->$k) . '\',';
        }
        $header = trim($header, ',') . ')';
        $values = trim($values, ',') . ')';
        return self::getDb()->query($header . $values);
    }

}