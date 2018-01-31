#!/usr/bin/env php
<?php
use Dowte\Password\pass\Password;
use \Dowte\Password\controllers\UserCommand;
use \Dowte\Password\controllers\InitCommand;
use \Dowte\Password\controllers\PasswordCommand;
use Dowte\Password\controllers\ListCommand;
use Dowte\Password\controllers\PassCommand;
// application.php

require __DIR__.'/../vendor/autoload.php';

defined('PASS_USER_CONF_DIR') or define('PASS_USER_CONF_DIR', __DIR__ . '/../data/');
defined('SQLITE_FILE_DIR') or define('SQLITE_FILE_DIR', __DIR__ . '/../data/');

use Symfony\Component\Console\Application;

$config = array_merge(
        require __DIR__ . '/config/main.php',
        require __DIR__ . '/config/main-local.php',
        require __DIR__ . '/config/params.php',
        require __DIR__ . '/config/params-local.php'
);
$app = new Password($config);

$console = new Application();

//Password::$params['private_key'] = $piKey =  openssl_pkey_get_private($privateKey);// 可用返回资源id
//Password::$params['public_key'] = $puKey = openssl_pkey_get_public($publicKey);

//openssl_public_encrypt('111', $encrypted, $puKey);//公钥加密
//die(base64_encode($encrypted));
//$encrypted = base64_encode($encrypted);// base64传输

//openssl_private_decrypt(base64_decode('c2ntkwJvNvsi+NMWR0944HPxoYzmPqpx5fSLxt3hri6vZKY3G5qdll2Pwe5LZkU9065hBocBO8CEYzdHUF52MXo6kBtKJfPGR0kPHSQAfFqlcBdw5OJflGzyQxnNXufIGGItacmhe6yxm0nTus2w/63RIgA51K8LpPTYkSx52T0='),
//    $decrypted, Password::getPrivateKey());//私钥解密
//die($decrypted);
//echo $decrypted,
//"<br/>";

// ... register commands
$index = 0;
foreach ($_SERVER['argv'] as &$v) {
    if (0 === $index || 1 === $index) {
        $index ++;
        continue;
    }
    $v = Password::encryptData($v);
}
$console->add(new InitCommand());
$console->add(new UserCommand());
$console->add(new PasswordCommand());
$console->add(new ListCommand());
$console->add(new PassCommand());

$console->run(null, (new \Dowte\Password\pass\SymfonyConsoleOutput()));

