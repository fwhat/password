<?php

namespace Dowte\Password\commands;

use Dowte\Password\forms\PasswordForm;
use Dowte\Password\pass\Password;
use Stecman\Component\Symfony\Console\BashCompletion\CompletionContext;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class PassCommand extends Command
{
    protected function configure()
    {
        $this->setName('name')
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
        $this->validPassword();
        $pass = PasswordForm::pass()->findOne(['password'], ['name' => $name]);
        if (! $pass || !isset($pass['password'])) {
            $this->_io->error('You provide password is wrong !');
        } else {
            Password::writePaste($pass['password'], $this->_io);
        }
    }

    protected function getArgumentName(CompletionContext $context)
    {
        $names = PasswordForm::pass()->getDecryptedName();

        return $names;
    }
}