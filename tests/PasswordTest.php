<?php
namespace Tests;

use Dowte\Password\pass\components\FileUtil;
use Dowte\Password\pass\PassSecret;
use Dowte\Password\pass\Password;
use Dowte\Password\pass\PasswordDb;

error_reporting(0);
define(CONF_FILE, __DIR__ . '/pass-conf-template.php');
defined('CONF_FILE_TEMP') or define('CONF_FILE_TEMP', realpath(__DIR__ . '/../pass-conf-template.php'));
defined('SQLITE_FILE') or define('SQLITE_FILE', FileUtil::_realPath(__DIR__ . '/../tests/sqlite_test.db'));

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

    private function init()
    {
        $this->pdb = new PasswordDb();
        $this->pdb->setWay(PasswordDb::SQLITE)->setConfigureFile(CONF_FILE)->init();
        $config = require CONF_FILE;
        Password::init($config);
        $this->secret = new PassSecret();
        $this->secret->setSecretKeyDir(__DIR__);
        $this->secret->buildSecretKey();
    }

    private function clear()
    {
        unlink(SQLITE_FILE);
    }

    public function testValidPassword()
    {
        $this->init();
        $password = PassSecret::encryptData(self::VALID_STR);
        $password2 = PassSecret::encryptData(self::VALID_STR);
        $this->assertTrue(PassSecret::validData($password, $password2));
        $this->secret->toTemplate(PassSecret::$privateKeyPath, PassSecret::$publicKeyPath);
        unlink(PassSecret::$publicKeyPath);
        unlink(PassSecret::$privateKeyPath);
        $this->pdb->clear();
        $this->clear();
    }
}