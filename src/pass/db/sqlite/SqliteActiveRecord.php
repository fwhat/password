<?php

namespace Dowte\Password\pass\db\sqlite;


use Dowte\Password\pass\db\ActiveQuery;
use Dowte\Password\pass\db\ActiveRecordInterface;
use Dowte\Password\pass\db\BaseActiveRecordInterface;
use Dowte\Password\pass\db\QueryInterface;

class SqliteActiveRecord extends Sqlite implements BaseActiveRecordInterface
{
    /**
     * @var ActiveQuery
     */
    public static $query;

    /**
     * @var ActiveRecordInterface
     */
    public static $model;

    /**
     * @var array
     */
    public static $data;

    public static $one = false;

    /**
     * SqliteActiveRecord constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        foreach ($config as $k => $value) {
            ! property_exists(__CLASS__, $k) or static::$$k = $value;
        }
        parent::init();
    }

    public function save()
    {
        $header = 'INSERT INTO ' . self::$model->name() . '(';
        $values = 'VALUES(';
        foreach (self::$model->attributeLabels() as $k => $v) {
//            $k = sqlite_escape_string($k);
//            $v = sqlite_escape_string($v);
            if ($k === 'id') continue;
            $header .= '`' . trim($k) . '`,';
            $values .= '\''. trim(self::$model->$k) . '\',';
        }
        $header = trim($header, ',') . ')';
        $values = trim($values, ',') . ')';
        return self::$db->query($header . $values);
    }

    /**
     * @return QueryInterface
     */
    public static function find()
    {
        return self::$query = (new SqliteQuery());
    }

    public static function findOne()
    {
        $sql = self::_getQuerySql();

        $sql .= ' LIMIT 1';

        $stmt = self::_prepare($sql);
        self::_fetchArray($stmt);
        return self::$data ? self::$data[0] : [];
    }

    public static function findAll()
    {
        $sql = self::_getQuerySql();

        $stmt = self::_prepare($sql);

        return self::_fetchArray($stmt);
    }

    private static function _getQuerySql()
    {
        $sql = sprintf("SELECT `%s` FROM `%s`", implode('`,`', self::$query->select), self::$model->name());
        if (self::$query->where) {
            $sql .= ' WHERE ';
            foreach (self::$query->where as $item => $value) {
                $sql .= sprintf("`%s`=:%s", $item, $item);
            }
        }
        return $sql;
    }

    /**
     * @param $sql
     * @return \SQLite3Result
     */
    private static function _prepare($sql)
    {
        $stmt = parent::$db->prepare($sql);
        if ($stmt) {
            foreach (self::$query->where as $item => $value) {
                //todo type
                $stmt->bindValue(':' . $item, $value, SQLITE3_TEXT);
            }
        }
        return $stmt->execute();
    }

    /**
     * @param $stmt \SQLite3Result
     * @return mixed
     */
    private static function _fetchArray(\SQLite3Result $stmt)
    {
        self::$data = [];
        while ($data = $stmt->fetchArray(SQLITE3_ASSOC)) {
            self::$data[] = $data;
        }
        return self::$data;
    }
}