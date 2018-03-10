<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

namespace Dowte\Password\pass\db\mysql;

use Dowte\Password\pass\db\ActiveRecord;
use Dowte\Password\pass\db\BaseActiveRecordInterface;
use Dowte\Password\pass\db\DbHelper;

class MysqlActiveRecord extends Mysql implements BaseActiveRecordInterface
{
    /**
     * @var ActiveRecord
     */
    public static $modelClass;

    /**
     * @var array
     */
    protected static $_data;

    /**
     * MysqlActiveRecord constructor.
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

    public function delete(array $conditions)
    {
        $where = [];
        $bindValues = [];
        foreach (self::$modelClass->attributeLabels() as $k => $v) {
            if (! isset(self::$modelClass->$k)) continue;
            $where[$k] = self::$modelClass->$k;
        }
        $sql = sprintf("DELETE FROM `%s` WHERE ", self::$modelClass->name());

        foreach ($where as $key => $value) {
            $sql .= sprintf(" `%s`=:%s AND", $key, $key);
            $bindValues[':' . $key] = $value;
        }
        $stmt = parent::getCon()->prepare(rtrim($sql, 'AND'));
        if ($stmt->execute($bindValues) === false) {
            DbHelper::$exception->error('The sql exec false : '. $stmt->queryString . PHP_EOL . print_r($stmt->errorInfo(), 1));
        }
        return $stmt->rowCount();
    }

    public static function execSql($sql)
    {
        $con = parent::getCon();
        if ($con->exec($sql) === false) {
            DbHelper::$exception->error('The sql exec false : '. $sql . PHP_EOL . print_r($con->errorInfo(), 1));
        }
    }

    protected function update(array $conditions = [])
    {
        $sql = 'UPDATE ' . self::$modelClass->name()  . ' SET ';
        $bindValues = [];
        foreach (self::$modelClass->attributeLabels() as $k => $v) {
            if ($k === 'id') continue;
            $sql .= sprintf("`%s`=:set%s", $k, $k);
            $bindValues[':set' . $k] = self::$modelClass->$k;
        }
        $where = ' WHERE';
        foreach ($conditions as $k => $value) {
            $where .= sprintf(" `%s`=:where%s AND", $k, $k);
            $bindValues[':where' . $k] = $value;
        }
        $stmt = parent::getCon()->prepare($sql . rtrim($where, 'AND'));
        if ($stmt->execute($bindValues) === false) {
            DbHelper::$exception->error('The sql exec false : '. $stmt->queryString . PHP_EOL . print_r($stmt->errorInfo(), 1));
        }
        return $stmt->rowCount();
    }

    protected function insert()
    {
        $bindValues = [];
        $header = 'INSERT INTO ' . self::$modelClass->name() . '(';
        $values = 'VALUES(';
        foreach (self::$modelClass->attributeLabels() as $k => $v) {
            if ($k === 'id') continue;
            $header .= sprintf("`%s`,", $k);
            $values .= sprintf(":%s,", $k);
            $bindValues[':' . $k] = self::$modelClass->$k;
        }
        $header = trim($header, ',') . ')';
        $values = trim($values, ',') . ')';
        $stmt = parent::getCon()->prepare($header . $values);
        if ($stmt->execute($bindValues) === false) {
            DbHelper::$exception->error('The sql exec false : '. $stmt->queryString . PHP_EOL . print_r($stmt->errorInfo(), 1));
        }
        return $stmt->rowCount();
    }
}