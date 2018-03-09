<?php

namespace Dowte\Password\pass;

use Dowte\Password\forms\UserForm;
use Dowte\Password\models\PasswordModel;
use Dowte\Password\models\UserModel;
use Dowte\Password\pass\components\FileUtil;
use Dowte\Password\pass\db\ActiveRecord;
use Dowte\Password\pass\db\sqlite\Sqlite;
use Dowte\Password\pass\db\yamlFile\Yaml;

class PasswordDb
{
    const SQLITE = 'sqlite';
    const MYSQL = 'mysql';
    const YAML_FILE = 'yamlFile';

    const DB_CLASS_MATCH = '%dbClass%';

    protected $_way;

    protected $_configureFile = CONF_FILE;

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
        $functionName = $this->_way . 'File';
        $this->clearDb();
        $this->toTemplate();
        unlink($this->_configureFile);
        foreach ($this->$functionName() as $value) {
            unlink($value);
        }
        return unlink(Password::getUserConfFile());
    }

    public function clearDb()
    {
        $functionName = $this->_way . 'Clear';
        $this->$functionName();
    }

    public function setConfigureFile($file)
    {
        ! file_exists($file) or $this->_configureFile = $file;
        return $this;
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

    protected function configureDb()
    {
        Password::rewriteConfig(self::DB_CLASS_MATCH, $this->_way, $this->_configureFile);
    }

    protected function toTemplate()
    {
        $config = str_replace(ActiveRecord::$className, str_replace($this->_way, self::DB_CLASS_MATCH, ActiveRecord::$className), file_get_contents($this->_configureFile));
        file_put_contents($this->_configureFile, $config);
    }

    private function sqliteInit()
    {
        $sqlite = new Sqlite();
        FileUtil::createFile($sqlite::getDbResource(Password::$pd->db->config['dbDir'], Password::$pd->db->config['dbName']));
        $sql = <<<EOF
CREATE TABLE IF NOT EXISTS user (
                    id INTEGER PRIMARY KEY, 
                    username VARCHAR(255) NOT NULL, 
                    password VARCHAR(255) NOT NULL)
EOF;

        $sqlite::getDb()->exec($sql);
        $sql = <<<EOF
CREATE TABLE IF NOT EXISTS password (
                    id INTEGER PRIMARY KEY, 
                    user_id INTEGER NOT NULL, 
                    keyword VARCHAR(255) NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    description VARCHAR(255) NOT NULL,
                    FOREIGN KEY(user_id) REFERENCES user(id)
                    )
EOF;
        $sqlite::getDb()->exec($sql);
    }

    private function sqliteClear()
    {
        $sql = '';
        $user = @file_get_contents(Password::getUserConfFile());
        $user = UserForm::user()->findOne(['username' => $user], ['id']);
        if (! $user) {
            return;
        }
        $userId = $user['id'];
        $sql .= sprintf("DELETE FROM password WHERE user_id = %d;\n", $userId);
        $sql .= sprintf("DELETE FROM user WHERE id = %d;\n", $userId);
        Sqlite::getDb()->exec($sql);
    }

    private function sqliteFile()
    {
        return [SQLITE_FILE];
    }

    private function yamlFileFile()
    {
        return [
            Yaml::getDbResource(DB_FILE_DIR, (new PasswordModel())->name()),
            Yaml::getDbResource(DB_FILE_DIR, (new UserModel())->name()),
        ];
    }

    private function yamlFileInit()
    {
        FileUtil::createFile($this->yamlFileFile());
    }

    private function yamlFileClear()
    {
        foreach ($this->yamlFileFile() as $file) {
            file_put_contents($file, '');
        }
    }
}