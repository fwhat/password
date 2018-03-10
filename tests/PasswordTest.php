<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

namespace Tests;

use Dowte\Password\pass\Password;
use Dowte\Password\pass\PasswordDb;

error_reporting(0);
define('CONF_FILE', __DIR__ . '/pass-conf-template.yaml');
defined('CONF_FILE_TEMP') or define('CONF_FILE_TEMP', realpath(__DIR__ . '/../pass-conf-template.yaml'));

class PasswordTest extends \PHPUnit\Framework\TestCase
{
    const VALID_STR = 'DOWTE';

    /**
     * @var PasswordDb
     */
    public $pdb;

    public $masterPassword = 'Dowte';

    public $tempString = 'Dowte\Password';

    public function testSymmetricalEncryption()
    {
        $str = Password::encryptPassword($this->masterPassword, $this->tempString);
        $enstr = Password::decryptedPassword($this->masterPassword, $str);
        $this->assertTrue($this->tempString === $enstr);
    }
}