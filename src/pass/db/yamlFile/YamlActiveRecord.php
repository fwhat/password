<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

namespace Dowte\Password\pass\db\yamlFile;

use Dowte\Password\pass\db\ActiveQuery;
use Dowte\Password\pass\db\ActiveRecordInterface;
use Dowte\Password\pass\db\BaseActiveRecordInterface;
use Dowte\Password\pass\db\DbHelper;

class YamlActiveRecord extends Yaml implements BaseActiveRecordInterface
{
    const PRIMARY_KEY = 'PRIMARY_KEY';

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
    protected static $DB_DIR;

    protected static $primaryKey;

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
        $primaryKey = self::getPrimaryKey();
        if (self::$modelClass->$primaryKey !== null) {
            return $this->updateOne([$primaryKey => self::$modelClass->$primaryKey]);
        } else {
            return $this->insert();
        }
    }

    public static function execSql($sql)
    {
        //create
        if (strpos(strtoupper($sql), 'CREATE') === 0) {
            if (preg_match('/\s([a-zA-Z]*)\s\(/', $sql, $res) && isset($res[1])) {
                if (file_exists(rtrim(self::$DB_DIR, '/') . '/' . self::getFromFile($res[1]))) {
                    DbHelper::$exception->error('The table ' . $res[1] . ' is already exists');
                }
                $resource = parent::getDbResource(self::$DB_DIR, $res[1]);

                if (preg_match('/\s.*PRIMARY/', strtoupper($sql), $res) && isset($res[0])) {
                    $str = explode(' ', trim($res[0]));
                    $primaryKey = array_shift($str);
                    parent::dumpInsertNote([self::PRIMARY_KEY => $primaryKey], $resource);
                }
            }
        }

        //drop
        if (strpos(strtoupper($sql), 'DROP') === 0) {
            $table = array_pop(explode(' ', $sql));
            unlink(parent::getDbResource(self::$DB_DIR, $table));
        }

        //other todo
    }

    public function delete(array $conditions)
    {
        $dbResource = self::getDbResource(self::$DB_DIR, self::$modelClass->name());
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
        $dbResource = self::getDbResource(self::$DB_DIR, self::$modelClass->name());
        $data = self::getData($dbResource);
        foreach ($data as &$item) {
            if ($this->compareConditions($conditions, $item)) {
                foreach (self::$modelClass->attributeLabels() as $k => $v) {
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
        $this->dumpInsertData($insertData, self::getDbResource(self::$DB_DIR, self::$modelClass->name()));
        return $insertData['id'];
    }

    /**
     * @return int
     */
    protected static function getNextId()
    {
        $data = self::getData(parent::getDbResource(self::$DB_DIR, self::$modelClass->name()));
        if (empty($data)) return 1;
        return ++array_pop($data)['id'];
    }

    protected function getPrimaryKey()
    {
        if (self::$primaryKey === null) {
            $fp =fopen(parent::getDbResource(self::$DB_DIR, self::$modelClass->name()), 'r');
            $line = ltrim(fgets($fp), '#');
            $description = \Symfony\Component\Yaml\Yaml::parse($line);
            self::$primaryKey = isset($description[self::PRIMARY_KEY]) ? $description[self::PRIMARY_KEY] : 'id';
        }

        return self::$primaryKey;
    }
}