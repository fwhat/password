<?php

namespace Dowte\Password\commands;

use Dowte\Password\forms\UserForm;
use Dowte\Password\pass\Password;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Dowte\Password\pass\SymfonyAsk;

class UserCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('create-user')

            // the short description shown while running "php bin/console list"
            ->setDescription('Creates a new user.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create a user...')
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userName = $input->getArgument('username');
        $helper = $this->getHelper('question');
        $question = new Question('Set a password for Pass?');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $password = SymfonyAsk::ask($helper, $input, $output, $question);
        $output->writeln([
            'User Creator',
            '============',
            '',
        ]);
        UserForm::user()->createUser($userName, $password);
        Password::userConf($userName);
    }
}