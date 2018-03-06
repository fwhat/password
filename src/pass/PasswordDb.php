<?php

namespace Dowte\Password\pass;

use Dowte\Password\forms\UserForm;
use Dowte\Password\models\PasswordModel;
use Dowte\Password\models\UserModel;
use Dowte\Password\pass\components\FileUtil;
use Dowte\Password\pass\db\sqlite\Sqlite;
use Dowte\Password\pass\db\yamlFile\Yaml;

class PasswordDb
{
    const SQLITE = 'sqlite';
    const MYSQL = 'mysql';
    const YAML_FILE = 'yamlFile';

    const DB_CLASS_MATCH = '%dbClass%';

    protected $_way;

    public function __construct()
    {
    }

    public static function ways()
    {
        return [self::SQLITE, self::YAML_FILE];
    }

    public function setWay($way)
    {
        if (! in_array($way, self::ways())) {
            Password::error('The way is not found!');
        }
        $this->_way = $way;
        return $this;
    }

    public function init()
    {
        $functionName = $this->_way . 'Init';
        $this->configureDb();
        $this->$functionName();
    }

    public function clear()
    {
        $functionName = $this->_way . 'Clear';
        $this->$functionName();
        $this->toTemplate();
        unlink(CONF_FILE);
        return unlink(Password::getUserConfFile());
    }

    /**
     * @return string
     */
    public function getDbWay()
    {
        foreach (explode('\\', Password::$dbClass) as $dbName) {
            if (in_array($dbName, self::ways())) {
                return $dbName;
            }
        }
        Password::error('The db way not found, please configure at first in ' . CONF_FILE);
    }

    protected function configureDb()
    {
        Password::rewriteConfig(self::DB_CLASS_MATCH, $this->_way);
    }

    protected function toTemplate()
    {
        $config = str_replace(str_replace($this->_way, self::DB_CLASS_MATCH, Password::$dbClass), Password::$dbClass, file_get_contents(CONF_FILE));
        file_put_contents(CONF_FILE, $config);
    }

    private function sqliteInit()
    {
        $sqlite = new Sqlite();
        FileUtil::createFile(SQLITE_FILE);
//        $sqlite::$dbKey = $dbKey;
        $sqlite->init();
        $sql = <<<EOF
CREATE TABLE IF NOT EXISTS user (
                    id INTEGER PRIMARY KEY, 
                    username VARCHAR(255) NOT NULL, 
                    password VARCHAR(255) NOT NULL)
EOF;

        $sqlite::$db->exec($sql);
        $sql = <<<EOF
CREATE TABLE IF NOT EXISTS password (
                    id INTEGER PRIMARY KEY, 
                    user_id INTEGER NOT NULL, 
                    name VARCHAR(255) NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    description VARCHAR(255) NOT NULL,
                    FOREIGN KEY(user_id) REFERENCES user(id)
                    )
EOF;
        $sqlite::$db->exec($sql);
    }

    private function sqliteClear()
    {
        $sql = '';
        $user = UserForm::user()->findOne(['username' => Password::getUser()], ['id']);
        if (! $user) {
            return;
        }
        $userId = $user['id'];
        $sql .= sprintf("DELETE FROM password WHERE user_id = %d;\n", $userId);
        $sql .= sprintf("DELETE FROM user WHERE id = %d;\n", $userId);
        Sqlite::$db->exec($sql);
    }

    private function yamlFileInit()
    {
        FileUtil::createFile(DB_FILE_DIR . Yaml::getFromFile((new UserModel())->name()));
        FileUtil::createFile(DB_FILE_DIR . Yaml::getFromFile((new PasswordModel())->name()));
    }

    private function yamlFileClear()
    {
        unlink(DB_FILE_DIR . Yaml::getFromFile((new UserModel())->name()));
        unlink(DB_FILE_DIR . Yaml::getFromFile((new PasswordModel())->name()));
    }
}