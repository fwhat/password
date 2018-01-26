<?php

namespace Dowte\Password\controllers;


use Dowte\Password\forms\UserForm;
use Dowte\Password\pass\Password;
use Dowte\Password\pass\SymfonyAsk;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class PasswordCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('pass:create-pass')

            // the short description shown while running "php bin/console list"
            ->setDescription('Creates a new password.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create a password...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new Question('What is the database password?');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $password = SymfonyAsk::ask($helper, $input, $output, $question);
        $user = UserForm::user()->findUser(Password::getUser(), $password);
        if (! $user) {
            //todo User Error
        }
    }
}