<?php

namespace Dowte\Password\pass\db\yamlFile;

use Dowte\Password\pass\db\ActiveQuery;
use Dowte\Password\pass\db\ActiveRecordInterface;
use Dowte\Password\pass\db\BaseActiveRecordInterface;
use Dowte\Password\pass\Password;

class YamlActiveRecord extends Yaml implements BaseActiveRecordInterface
{
    /**
     * @var ActiveQuery
     */
    protected static $_query;

    /**
     * @var ActiveRecordInterface
     */
    protected static $modelClass;

    /**
     * @var array
     */
    protected static $_data;

    protected static $dbDir;

    /**
     * SqliteActiveRecord constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        foreach ($config as $k => $value) {
            ! property_exists(__CLASS__, $k) or static::$$k = $value;
        }
    }

    public function save()
    {
        $insertData = [];
        foreach (self::$modelClass->attributeLabels() as $k => $v) {
            if ($k === 'id') continue;
            $insertData[$k] = self::$modelClass->$k;
        }
        $insertData['id'] = self::getNextId();
        self::dumpInsertData($insertData, self::getDbResource());
        return $insertData['id'];
    }

    public static function find()
    {
        return self::$_query = new YamlQuery();
    }

    public static function findOne()
    {
        return self::findByWhere(self::getQuery()->where);
    }

    public static function findAll()
    {
        return self::findByWhere(self::getQuery()->where, false);
    }

    protected static function findByWhere($where, $one = true)
    {
        $findArr = [];
        $data = self::getData(self::getDbResource());
        $beforeSelectData = $data;
        $itemArr = [];
        foreach (self::$modelClass->attributeLabels() as $k => $v) {
            foreach ($data as &$item) {
                $itemArr[$k][] = isset($item[$k]) ? $item[$k] : '';
                if (self::getQuery()->select !== (self::getQuery())::DEFAULT_SELECT && ! in_array($k, self::getQuery()->select)) {
                    unset($item[$k]);
                }
            }
        }
        foreach ($where as $key => $value) {
            if (! isset($itemArr[$key])) {
                Password::error('The property ' . $key . ' is not exist');
            }
            foreach ($itemArr[$key] as $k => $v) {
                if ($value === $v) {
                    if (self::compareDataAndWhere($beforeSelectData[$k], $where)) {
                        if ($one) return $data[$k];
                        $findArr[] = $data[$k];
                    }
                }
            }
        }

        foreach ($findArr as &$v) {
            foreach ($v as &$item) {
                if ($item ===  null) {
                    $item = '';
                }
            }
        }

        return $findArr;
    }

    protected static function getNextId()
    {
        $data = self::getData(self::getDbResource());
        if (empty($data)) return 1;
        return ++array_pop($data)['id'];
    }

    protected static function compareDataAndWhere($data, $where)
    {
        foreach ($where as $k => $value) {
            if ($data[$k] !== $value) {
                return false;
            }
        }
        
        return true;
    }

    protected static function getDbResource()
    {
        return self::$dbDir . self::getFromFile(self::$modelClass->name());
    }

    /**
     * @return ActiveQuery
     */
    protected static function getQuery()
    {
        return self::$_query;
    }
}