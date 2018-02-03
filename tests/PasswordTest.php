<?php
error_reporting(0);
define(CONF_FILE, __DIR__ . '/pass-conf-template.php');

use Dowte\Password\pass\PassSecret;

class PasswordTest extends \PHPUnit\Framework\TestCase
{
    const VALID_STR = 'DOWTE';

    /**
     * @var PassSecret
     */
    public $secret;

    private function init()
    {
        $config = require (__DIR__ . '/pass-conf-template.php');
        \Dowte\Password\pass\Password::init($config);
        $this->secret = new \Dowte\Password\pass\PassSecret();
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