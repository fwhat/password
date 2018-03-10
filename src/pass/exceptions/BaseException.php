<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

namespace Dowte\Password\pass\exceptions;


class BaseException extends \Exception
{
    const QUERY_CODE = 10001;
    const USER_CODE = 10002;
}