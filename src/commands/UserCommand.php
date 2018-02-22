<?php

namespace Dowte\Password\commands;

use Dowte\Password\forms\UserForm;
use Dowte\Password\pass\Password;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class UserCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('c-user')

            // the short description shown while running "php bin/console list"
            ->setDescription('Creates a new user.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create a user...')
            ->addArgument('username', InputArgument::OPTIONAL, 'New username for password');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userName = $input->getArgument('username');
        if (! $userName) {
            $helper = $this->getHelper('question');
            $question = new Question('Set a name for Pass?');
            $userName = $helper->ask($input, $output, $question);
        }
        $helper = $this->getHelper('question');
        $question = new Question('Set a password for Pass?');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $password = $this->encryptAsk($helper, $question);
        if (! $password) {
            $this->_io->error('Password could\'t be empty');
        } else {
            $userName = UserForm::user()->createUser($userName, $password);
            Password::userConfigure($userName);
            $this->_io->success('User created ! please protect the user conf in ' . realpath(Password::getUserConfFile()));
        }
    }
}