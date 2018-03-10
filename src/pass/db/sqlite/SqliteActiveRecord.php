<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

namespace Dowte\Password\pass\db\sqlite;

use Dowte\Password\pass\db\ActiveRecord;
use Dowte\Password\pass\db\BaseActiveRecordInterface;
use Dowte\Password\pass\db\DbHelper;

class SqliteActiveRecord extends Sqlite implements BaseActiveRecordInterface
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
     * SqliteActiveRecord constructor.
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

    public static function execSql($sql)
    {
        if (strpos(strtoupper($sql), 'CREATE') === 0) {
            $sql = str_replace(['AUTO_INCREMENT', 'auto_increment'], '', $sql);
        }
        //删库
        if (strpos(strtoupper($sql), 'DROP DATABASE') === 0) {
            $table = array_pop(explode(' ', $sql));
            return unlink(parent::getDbResource(Connection::$config['DB_DIR'], $table));
        }
        $con = parent::getDb();
        $stmt = $con->prepare($sql);
        if ($stmt->execute() === false) {
            DbHelper::$exception->error('The sql exec false :' . $sql);
        }
        return true;
    }

    public function delete(array $conditions)
    {
        $where = [];
        $bindValues = [];
        foreach (self::$modelClass->attributeLabels() as $k => $v) {
            if (! isset($conditions[$k])) continue;
            $where[$k] = self::$modelClass->$k;
        }
        $sql = sprintf("DELETE FROM `%s` WHERE ", self::$modelClass->name());

        foreach ($where as $key => $value) {
            $sql .= sprintf(" `%s`=:%s AND", $key, $key);
            $bindValues[':' . $key] = $value;
        }
        $stmt = self::getDb()->prepare(rtrim($sql, 'AND'));
        foreach ($bindValues as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        return $stmt->execute();
    }

    protected function insert()
    {
        $header = 'INSERT INTO ' . self::$modelClass->name() . '(';
        $bindValues = [];
        $values = 'VALUES(';
        foreach (self::$modelClass->attributeLabels() as $k => $v) {
            if ($k === 'id') continue;
            $header .= sprintf("`%s`,", $k);
            $values .= sprintf(":%s,", $k);
            $bindValues[':' . $k] = self::$modelClass->$k;
        }
        $header = trim($header, ',') . ')';
        $values = trim($values, ',') . ')';
        $stmt = self::getDb()->prepare($header . $values);
        foreach ($bindValues as $key => $value){
            $stmt->bindValue($key, $value);
        }

        return $stmt->execute();
    }

    protected function update($conditions)
    {
        $sql = 'UPDATE ' . self::$modelClass->name()  . ' SET ';
        $bindValue = [];
        foreach (self::$modelClass->attributeLabels() as $k => $v) {
            if ($k === 'id') continue;
            $sql .= sprintf("`%s`=:set%s", $k, $k);
            $bindValue[':set' . $k] = $k;
        }
        $where = ' WHERE';
        foreach ($conditions as $k => $value) {
            $where .= sprintf(" `%s`=:where%s AND", $k, $k);
            $bindValue[':where' . $k] = $k;
        }
        $stmp = self::getDb()->prepare($sql . rtrim($where, 'AND'));
        foreach ($bindValue as $k => $value) {
            $stmp->bindValue($k, $value);
        }

        return $stmp->execute();
    }

}