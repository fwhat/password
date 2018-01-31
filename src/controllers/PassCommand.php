<?php

namespace Dowte\Password\controllers;

use Dowte\Password\forms\PasswordForm;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class PassCommand extends Command
{
    protected function configure()
    {
        $this->setName('pass')
            ->setDescription('Get a pass by name')
            ->setHelp('This command allows you to get a password...')
            ->addArgument('name', InputArgument::OPTIONAL, 'The username of the user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        if (! $name) {
            $helper = $this->getHelper('question');
            $question = new Question('Which name is you want to get:' . PHP_EOL);
            $name = $helper->ask($input, $output, $question);
        }
        $pass = PasswordForm::pass()->findOne(['password'], ['name' => $name]);
        $output->writePaste($pass['password']);
    }
}