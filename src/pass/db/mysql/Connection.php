<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

namespace Dowte\Password\pass\db\mysql;


use Dowte\Password\pass\db\BaseConnection;

class Connection extends BaseConnection
{
    protected function setActiveRecordClass()
    {
        $this->_activeRecordClass = MysqlActiveRecord::class;
    }

    protected function allowProperties()
    {
        return ['DB_DSN', 'DB_USER', 'DB_PASS', 'ENGINE', 'CHARSET'];
    }

    public static function requireProperties()
    {
        return ['DB_DSN', 'DB_USER', 'DB_PASS'];
    }

    protected function setActiveQueryClass()
    {
        $this->_activeQueryClass = MysqlQuery::class;
    }

    protected function defaultConfigs()
    {
        return['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8'];
    }
}