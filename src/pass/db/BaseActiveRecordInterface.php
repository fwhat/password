<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

namespace Dowte\Password\pass\db;


interface BaseActiveRecordInterface
{
    /**
     * @return integer;
     */
    public function save();

    /**
     * @param array $conditions
     * @return bool
     */
    public function delete(array $conditions);

    /**
     * @param $sql
     * @return bool
     */
    public static function execSql($sql);
}