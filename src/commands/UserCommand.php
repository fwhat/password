<?php

namespace Dowte\Password\commands;

use Dowte\Password\forms\UserForm;
use Dowte\Password\pass\Password;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class UserCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('user')
            ->setAliases(['u'])

            // the short description shown while running "php bin/console list"
            ->setDescription('Creates a new user.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create a user...')
            ->addOption('username', 'u', InputOption::VALUE_OPTIONAL, 'New username for password')
            ->addOption('fix', 'f',InputOption::VALUE_NONE, 'Fix user-conf if miss the user-config');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userName = $input->getOption('username');
        $fix = $input->getOption('fix');
        $askNameQuestion = $fix ? 'What\'s your old username?' : 'Set a name for Pass?';
        if (! $userName) {
            $helper = $this->getHelper('question');
            $question = new Question($askNameQuestion);
            $userName = $helper->ask($input, $output, $question);
        }
        if ($fix) {
            $encryptName = Password::encryptUserName($userName);
            if ($this->validPassword('', $encryptName)) {
                Password::userConfigure($encryptName);
            }
        } else {
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
                $this->_io->success('User created !');
            }
        }
    }
}