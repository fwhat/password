<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

namespace Dowte\Password\pass\db;


interface QueryInterface
{
    /**
     * @param $select
     * @return QueryInterface
     */
    public function select($select);

    /**
     * @param $where
     * @return QueryInterface
     */
    public function where($where);

    /**
     * @return array
     */
    public function one();

    /**
     * @return array
     */
    public function all();
}