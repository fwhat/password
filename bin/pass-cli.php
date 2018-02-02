#!/usr/bin/env php
<?php
use Symfony\Component\Console\Application;
use Dowte\Password\pass\Password;
use Dowte\Password\commands\UserCommand;
use Dowte\Password\commands\DefaultCommand;
use Dowte\Password\commands\PasswordCommand;
use Dowte\Password\commands\ListCommand;
use Dowte\Password\commands\PassCommand;
use Dowte\Password\commands\InitCommand;
use Dowte\Password\commands\CompletionCommand;
use Dowte\Password\commands\ClearCommand;

// application.php

require __DIR__ . '/../vendor/autoload.php';

defined('PASS_USER_CONF_DIR') or define('PASS_USER_CONF_DIR', __DIR__ . '/../data/');
defined('SQLITE_FILE_DIR') or define('SQLITE_FILE_DIR', __DIR__ . '/../data/');
defined('SQLITE_FILE') or define('SQLITE_FILE', __DIR__ . '/../data/pass.db');

$config = array_merge(
        require __DIR__ . '/../src/config/main.php',
        require __DIR__ . '/../src/config/main-local.php',
        require __DIR__ . '/../src/config/params.php',
        require __DIR__ . '/../src/config/params-local.php'
);
$app = new Password($config);

$console = new Application();


// ... register commands
$console->add(new InitCommand());
$console->add(new CompletionCommand());
$console->add(new DefaultCommand());
$console->add(new UserCommand());
$console->add(new PasswordCommand());
$console->add(new ListCommand());
$console->add(new PassCommand());
$console->add(new ClearCommand());

$console->run(null, (new \Dowte\Password\pass\SymfonyConsoleOutput()));

