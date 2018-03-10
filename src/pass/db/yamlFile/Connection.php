<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

namespace Dowte\Password\pass\db\yamlFile;

use Dowte\Password\pass\db\BaseConnection;

class Connection extends BaseConnection
{
    protected function setActiveRecordClass()
    {
        $this->_activeRecordClass = YamlActiveRecord::class;
    }

    protected function allowProperties()
    {
        return ['DB_DIR'];
    }

    protected function setActiveQueryClass()
    {
        $this->_activeQueryClass = YamlQuery::class;
    }

    public static function requireProperties()
    {
        return ['DB_DIR'];
    }
}