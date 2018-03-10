<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

namespace Dowte\Password\pass\db;

interface ActiveRecordInterface extends BaseActiveRecordInterface
{
    /**
     * @return array
     */
    public function attributeLabels();

    /**
     * @return string
     */
    public function name();

    /**
     * @return array
     */
    public function rules();

    public static function find();
}