<?php

namespace Dowte\Password\pass\db\sqlite;


use Dowte\Password\pass\db\ActiveQuery;

class SqliteQuery extends ActiveQuery
{
    protected static $_data;

    public function one()
    {
        $sql = $this->getQuerySql();

        $sql .= ' LIMIT 1';

        $stmt = $this->prepare($sql);
        self::_fetchArray($stmt);
        return self::$_data ? self::$_data[0] : [];
    }

    public function all()
    {
        $sql = $this->getQuerySql();

        $stmt = $this->prepare($sql);

        return self::_fetchArray($stmt);
    }

    private function getQuerySql()
    {
        $query = $this->select == parent::DEFAULT_SELECT ? '*' : '`' . implode('`,`', $this->select) . '`';
        $sql = sprintf("SELECT %s FROM `%s`", $query, $this->modelClass->name());
        if ($this->where) {
            $sql .= ' WHERE ';
            foreach ($this->where as $item => $value) {
                if (is_array($value) && in_array(($key = array_shift($value)), $this->keyWords)) {
                    $sql .= sprintf("`%s` %s '%s' AND", array_shift($value), $key, array_shift($value));

                } else {
                    $sql .= sprintf("`%s`=:%s AND", $item, $item);
                }
            }
        }
        return rtrim($sql, 'AND');
    }

    /**
     * @param $sql
     * @return \SQLite3Result
     */
    private function prepare($sql)
    {
        $stmt = Sqlite::getDb()->prepare($sql);
        if ($stmt) {
            foreach ($this->where as $item => $value) {
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