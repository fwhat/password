<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

namespace Dowte\Password\pass\components;

use Dowte\Password\pass\Password;


/**
 * Class PdHelper
 * @package Dowte\Password\pass\components
 * @property array $params
 * @property \Dowte\Password\pass\db\BaseConnection $db
 * @property \Dowte\Password\pass\PasswordGenerate $generate
 */
class PdHelper
{
    protected static $static = [];

    public function __get($name)
    {
        if (! isset(self::$static[$name])) {
            Password::error('The property ' . $name . ' is not exists');
        }
        return self::$static[$name];
    }

    public function __set($name, $value)
    {
        self::$static[$name] = $value;
    }
}