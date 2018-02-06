<?php
namespace Tests;
error_reporting(0);
define(CONF_FILE, __DIR__ . '/pass-conf-template.php');
defined('CONF_FILE_TEMP') or define('CONF_FILE_TEMP', realpath(__DIR__ . '/../pass-conf-template.php'));

use Dowte\Password\pass\PassSecret;
use Dowte\Password\pass\Password;

class PasswordTest extends \PHPUnit\Framework\TestCase
{
    const VALID_STR = 'DOWTE';

    /**
     * @var PassSecret
     */
    public $secret;

    private function init()
    {
        $config = require CONF_FILE_TEMP;
        Password::init($config);
        $this->secret = new PassSecret();
        $this->secret->setSecretKeyDir(__DIR__);
        $this->secret->buildSecretKey();
    }

    public function testValidPassword()
    {
        $this->init();
        $password = PassSecret::encryptData(self::VALID_STR);
        $password2 = PassSecret::encryptData(self::VALID_STR);
        $this->assertTrue(PassSecret::validData($password, $password2));
        $this->secret->toSecretKeyTemplate(PassSecret::$privateKeyPath, PassSecret::$publicKeyPath);
        unlink(PassSecret::$publicKeyPath);
        unlink(PassSecret::$privateKeyPath);
    }
}