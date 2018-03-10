<?php

namespace Dowte\Password\pass\db\yamlFile;

use Dowte\Password\pass\db\ActiveQuery;
use Dowte\Password\pass\db\ActiveRecordInterface;
use Dowte\Password\pass\db\BaseActiveRecordInterface;

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

    /**
     * @var string
     */
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

    /**
     * @return bool|int
     */
    public function save()
    {
        //PRIMARY KEY
        if (self::$modelClass->id !== null) {
            return $this->updateOne(['id' => self::$modelClass->id]);
        } else {
            return $this->insert();
        }
    }

    public function delete(array $conditions)
    {
        $dbResource = self::getDbResource(self::$dbDir, self::$modelClass->name());
        $data = self::getData($dbResource);
        foreach ($data as $k => $item) {
            if ($this->compareConditions($conditions, $item)) {
                unset($data[$k]);
            }
        }
        return $this->updateResource(array_values($data), $dbResource);
    }

    /**
     * @param array $conditions
     * @return bool|int
     */
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
                return $this->updateResource($data, $dbResource);
            }
        }


        return false;
    }

    /**
     * @param $conditions
     * @param $data
     * @return bool
     */
    protected function compareConditions($conditions, $data)
    {
        foreach ($conditions as $k => $v) {
            if (!isset($data[$k]) || $data[$k] !== $v) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return int
     */
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

    /**
     * @return int
     */
    protected static function getNextId()
    {
        $data = self::getData(parent::getDbResource(self::$dbDir, self::$modelClass->name()));
        if (empty($data)) return 1;
        return ++array_pop($data)['id'];
    }
}