<?php

namespace Dowte\Password\controllers;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class InitCommand extends Command
{
    protected function configure()
    {
        $this->setName('pass:init')
            // the short description shown while running "php bin/console list"
            ->setDescription('Initialize PASSWORD')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to initialize PASSWORD');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            '请选择储存密码文件的方式 (默认本地)',
            array('本地', '服务器'),
            0
        );
        $question->setErrorMessage('the way %s is invalid.');
        $way = $helper->ask($input, $output, $question);
    }
}