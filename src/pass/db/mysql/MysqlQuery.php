<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

namespace Dowte\Password\pass\db\mysql;

use \PDO;
use Dowte\Password\pass\db\ActiveQuery;

class MysqlQuery extends ActiveQuery
{
    protected static $_data;

    public function one()
    {
        $sql = $this->getQuerySql();

        $sql .= ' LIMIT 1';

        $stmt = $this->prepare($sql);
        if ($stmt->execute()) {
            self::$_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return self::$_data ? self::$_data[0] : [];
    }

    public function all()
    {
        $sql = $this->getQuerySql();

        $stmt = $this->prepare($sql);
        if ($stmt->execute()) {
            self::$_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return self::$_data;
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
     * @return \PDOStatement
     */
    private function prepare($sql)
    {
        $stmt = Mysql::getCon()->prepare($sql);
        if ($stmt) {
            foreach ($this->where as $item => $value) {
                //todo type
                $stmt->bindValue(':' . $item, $value);
            }
        }
        return $stmt;
    }
}