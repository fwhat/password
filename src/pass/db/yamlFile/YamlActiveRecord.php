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
        //PRIMARY KEY
        if (self::$modelClass->id !== null) {
            return $this->updateOne(['id' => self::$modelClass->id]);
        } else {
            return $this->insert();
        }
    }

    protected function updateOne(array $conditions = [])
    {
        $dbResource = self::getDbResource(self::$dbDir, self::$modelClass->name());
        $data = self::getData($dbResource);
        foreach ($data as &$item) {
            if ($this->compareConditions($conditions, $item)) {
                foreach (self::$modelClass->attributeLabels() as $k => $v) {
                    if ($k === 'id') continue;
                    !self::$modelClass->$k or $item[$k] = self::$modelClass->$k;
                }
                return $this->updateData($data, $dbResource);
            }
        }


        return false;
    }

    protected function compareConditions($conditions, $data)
    {
        foreach ($conditions as $k => $v) {
            if (!isset($data[$k]) || $data[$k] !== $v) {
                return false;
            }
        }

        return true;
    }

    protected function insert()
    {
        foreach (self::$modelClass->attributeLabels() as $k => $v) {
            if ($k === 'id') continue;
            $insertData[$k] = self::$modelClass->$k;
        }
        $insertData['id'] = self::getNextId();
        $this->dumpInsertData($insertData, self::getDbResource(self::$dbDir, self::$modelClass->name()));
        return $insertData['id'];
    }

    protected static function getNextId()
    {
        $data = self::getData(parent::getDbResource(self::$dbDir, self::$modelClass->name()));
        if (empty($data)) return 1;
        return ++array_pop($data)['id'];
    }
}