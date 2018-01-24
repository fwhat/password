<?php

namespace Dowte\Password\base;


use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class SymfonyAsk
{
    public static function ask(QuestionHelper $helper, InputInterface $input, OutputInterface $output, Question $question)
    {
        $messages = $helper->ask($input, $output, $question);
        openssl_private_decrypt(base64_decode($messages), $messages, Password::$params['private_key']);
        return $messages;
    }
}