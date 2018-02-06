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
    protected static $_query;

    /**
     * @var ActiveRecordInterface
     */
    protected static $_model;

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
        parent::init();
    }

    public function save()
    {
        $header = 'INSERT INTO ' . self::$_model->name() . '(';
        $values = 'VALUES(';
        foreach (self::$_model->attributeLabels() as $k => $v) {
//            $k = sqlite_escape_string($k);
//            $v = sqlite_escape_string($v);
            if ($k === 'id') continue;
            $header .= '`' . trim($k) . '`,';
            $values .= '\''. trim(self::$_model->$k) . '\',';
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
        return self::$_query = (new SqliteQuery());
    }

    public static function findOne()
    {
        $sql = self::_getQuerySql();

        $sql .= ' LIMIT 1';

        $stmt = self::_prepare($sql);
        self::_fetchArray($stmt);
        return self::$_data ? self::$_data[0] : [];
    }

    public static function findAll()
    {
        $sql = self::_getQuerySql();

        $stmt = self::_prepare($sql);

        return self::_fetchArray($stmt);
    }

    private static function _getQuerySql()
    {
        $query = self::$_query->select == ActiveQuery::DEFAULT_SELECT ? '*' : '`' . implode('`,`', self::$_query->select) . '`';
        $sql = sprintf("SELECT %s FROM `%s`", $query, self::$_model->name());
        if (self::$_query->where) {
            $sql .= ' WHERE ';
            foreach (self::$_query->where as $item => $value) {
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
            foreach (self::$_query->where as $item => $value) {
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
        self::$_data = [];
        while ($data = $stmt->fetchArray(SQLITE3_ASSOC)) {
            self::$_data[] = $data;
        }
        return self::$_data;
    }
}