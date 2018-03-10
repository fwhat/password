<?php
namespace Tests;

use Dowte\Password\pass\components\FileUtil;
use Dowte\Password\pass\PassSecret;
use Dowte\Password\pass\Password;
use Dowte\Password\pass\PasswordDb;

error_reporting(0);
define(CONF_FILE, __DIR__ . '/pass-conf-template.php');
defined('CONF_FILE_TEMP') or define('CONF_FILE_TEMP', realpath(__DIR__ . '/../pass-conf-template.php'));
defined('SQLITE_FILE') or define('SQLITE_FILE', FileUtil::realPath(__DIR__ . '/../tests/sqlite_test.db'));

class PasswordTest extends \PHPUnit\Framework\TestCase
{
    const VALID_STR = 'DOWTE';

    /**
     * @var PassSecret
     */
    public $secret;

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