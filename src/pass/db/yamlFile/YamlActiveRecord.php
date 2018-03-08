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
        self::dumpInsertData($insertData, self::getDbResource(self::$dbDir, self::$modelClass->name()));
        return $insertData['id'];
    }

    protected static function getNextId()
    {
        $data = self::getData(parent::getDbResource(self::$dbDir, self::$modelClass->name()));
        if (empty($data)) return 1;
        return ++array_pop($data)['id'];
    }
}