<?php

namespace Dowte\Password\controllers;


use Dowte\Password\pass\PassInit;
use Dowte\Password\pass\SymfonyAsk;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class InitCommand extends Command
{
    protected function configure()
    {
        $this->setName('init')
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
            ['普通文件', 'sqlite', 'mysql'],
            0
        );
        $question->setErrorMessage('the way %s is invalid.');
        $way = $helper->ask($input, $output, $question);
        $passInit = new PassInit();
        switch ($way) {
            case '1' :
                break;
            case 'sqlite' :
                //todo 加密
//                $question = new Question('请输入密码用于加密数据库和数据');
//                $question->setHidden(true);
//                $question->setHiddenFallback(false);
//                $password = SymfonyAsk::ask($helper, $input, $output, $question);
//                $passInit->InitSqlite($password);
                $passInit->InitSqlite();
                break;
            default :
                echo 2;
        }
    }
}