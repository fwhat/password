<?php

namespace Dowte\Password\pass\db\sqlite;


use Dowte\Password\pass\db\ActiveQuery;
use Dowte\Password\pass\db\ActiveRecord;
use Dowte\Password\pass\db\ActiveRecordInterface;
use Dowte\Password\pass\db\BaseActiveRecordInterface;

class SqliteActiveRecord extends Sqlite implements BaseActiveRecordInterface
{
    /**
     * @var ActiveQuery
     */
    protected static $_query;

    /**
     * @var ActiveRecord
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
        if (self::$modelClass->id !== null) {
            return $this->update(['id' => self::$modelClass->id]);
        } else {
            return $this->insert();
        }
    }

    protected function insert()
    {
        $header = 'INSERT INTO ' . self::$modelClass->name() . '(';
        $values = 'VALUES(';
        foreach (self::$modelClass->attributeLabels() as $k => $v) {
            if ($k === 'id') continue;
            $header .= sprintf("`%s`,", trim($k));
            $values .= sprintf("'%s',", trim(self::$modelClass->$k));
        }
        $header = trim($header, ',') . ')';
        $values = trim($values, ',') . ')';
        return self::getDb()->query($header . $values);
    }

    protected function update($conditions)
    {
        $sql = 'UPDATE ' . self::$modelClass->name()  . ' SET ';

        foreach (self::$modelClass->attributeLabels() as $k => $v) {
            if ($k === 'id') continue;
            $sql .= sprintf("`%s`='%s'", $k, self::$modelClass->$k);
        }
        $where = ' WHERE';
        foreach ($conditions as $k => $value) {
            $where .= sprintf("`%s`='%s' AND", $k, $value);
        }

        return self::getDb()->query($sql . rtrim($where, 'AND'));
    }

}