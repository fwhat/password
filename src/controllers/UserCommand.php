<?php

namespace Dowte\Password\controllers;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Dowte\Password\base\SymfonyAsk;

class UserCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('pass:create-user')

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
        $question = new Question('What is the database password?');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $password = SymfonyAsk::ask($helper, $input, $output, $question);
        // outputs a message followed by a "\n"
        $output->writeln([
            'User Creator',
            '============',
            '',
        ]);
        $output->writePaste($userName);
    }
}