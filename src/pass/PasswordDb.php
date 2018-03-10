<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

namespace Dowte\Password\pass;

use Dowte\Password\forms\PasswordForm;
use Dowte\Password\forms\UserForm;
use Dowte\Password\pass\db\ActiveRecord;

class PasswordDb
{
    const SQLITE = 'sqlite';
    const MYSQL = 'mysql';
    const YAML_FILE = 'yamlFile';

    protected $_way;

    protected $_configureFile = CONF_FILE;

    public function __construct()
    {
    }

    /**
     * @return array
     */
    public static function ways()
    {
        return [self::SQLITE, self::YAML_FILE, self::MYSQL];
    }

    /**
     * @param $way
     * @return $this
     */
    public function setWay($way)
    {
        if (! in_array($way, self::ways())) {
            Password::error('The way is not found!');
        }
        $this->_way = $way;
        return $this;
    }

    /**
     * @param $user
     * @return bool
     */
    public function clear($user)
    {
        $this->dbClear($user);
        $this->dropTable();

        //删除alfred 配置 和用户配置
        @unlink(ALFRED_CONF_FILE);
        @unlink(Password::getUserConfFile());
        return true;
    }

    /**
     * @return string
     */
    public function getDbWay()
    {
        foreach (explode('\\', ActiveRecord::$className) as $dbName) {
            if (in_array($dbName, self::ways())) {
                return $dbName;
            }
        }
        Password::error('The db way not found, please configure at first in ' . CONF_FILE);
    }

    public function dbInit()
    {
        $sql = <<<EOF
CREATE TABLE IF NOT EXISTS user (
                    id INTEGER AUTO_INCREMENT PRIMARY KEY, 
                    username VARCHAR(255) NOT NULL, 
                    password VARCHAR(255) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOF;
        ActiveRecord::execSql($sql);
        $sql = <<<EOF
CREATE TABLE IF NOT EXISTS password (
                    id INTEGER AUTO_INCREMENT PRIMARY KEY, 
                    user_id INTEGER NOT NULL, 
                    keyword VARCHAR(255) NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    description VARCHAR(255) NOT NULL,
                    FOREIGN KEY(user_id) REFERENCES user(id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOF;
        ActiveRecord::execSql($sql);
    }

    public function dbClear($user)
    {
        if (! $user || ! isset($user['id'])) {
            return false;
        }
        PasswordForm::pass()->deleteByConditions(['user_id' => $user['id']]);
        UserForm::user()->delete($user['id']);
        unlink(Password::getUserConfFile());
        return true;
    }

    public function dropTable()
    {
        ActiveRecord::execSql("DROP TABLE IF EXISTS password");
        ActiveRecord::execSql("DROP TABLE IF EXISTS user");
        if (isset(Password::$pd->db->config['DB_NAME']) && ! empty(Password::$pd->db->config['DB_NAME'])) {
            ActiveRecord::execSql("DROP DATABASE IF EXISTS " . Password::$pd->db->config['DB_NAME']);
        }
    }
}