#!/usr/bin/env php
<?php

use Dowte\Password\pass\Password;
use \Dowte\Password\controllers\UserCommand;
use \Dowte\Password\controllers\InitCommand;
use \Dowte\Password\controllers\PasswordCommand;
// application.php

require __DIR__.'/../vendor/autoload.php';

defined('PASS_USER_CONF_DIR') or define('PASS_USER_CONF_DIR', __DIR__ . '/../data/');

use Symfony\Component\Console\Application;

$config = array_merge(require __DIR__ . '/config/params.php', require __DIR__ . '/config/main.php');
$app = new Password($config);

$console = new Application();

$privateKey = file_get_contents(Password::$params['private_key']);
$publicKey = file_get_contents(Password::$params['public_key']);

Password::$params['private_key'] = $piKey =  openssl_pkey_get_private($privateKey);// 可用返回资源id
Password::$params['public_key'] = $puKey = openssl_pkey_get_public($publicKey);

//openssl_public_encrypt($data, $encrypted, $pu_key);//公钥加密
//$encrypted = base64_encode($encrypted);// base64传输

//openssl_private_decrypt(base64_decode($encrypted), $decrypted, $pi_key);//私钥解密
//echo $decrypted,"<br/>";

// ... register commands
$index = 0;
foreach ($_SERVER['argv'] as &$v) {
    if (0 === $index || 1 === $index) {
        $index ++;
        continue;
    }
    openssl_public_encrypt($v, $encrypted, $puKey);
    $v = base64_encode($encrypted);
}
$console->add(new InitCommand());
$console->add(new UserCommand());
$console->add(new PasswordCommand());

$console->run(null, (new \Dowte\Password\pass\SymfonyConsoleOutput()));

