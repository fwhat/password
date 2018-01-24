<?php

namespace Dowte\Password\base;

use Symfony\Component\Console\Output\ConsoleOutput;

class SymfonyConsoleOutput extends ConsoleOutput
{
    public function writePaste($messages, $description = '复制剪切板成功')
    {
        openssl_private_decrypt(base64_decode($messages), $messages, Password::$params['private_key']);

        parent::write($description, true, self::OUTPUT_NORMAL);
    }
}