<?php

namespace Dowte\Password\commands;

use Dowte\Password\forms\PasswordForm;
use Dowte\Password\pass\Password;
use Stecman\Component\Symfony\Console\BashCompletion\CompletionContext;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class PassCommand extends Command
{
    protected function configure()
    {
        $this->setName('name')
            ->setDescription('Get a pass by name')
            ->setHelp('This command allows you to get a password...')
            ->addOption('name', 'N', InputOption::VALUE_OPTIONAL, 'Get password from the password name.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $this->encryptOption('name');
        if (! $name) {
            $helper = $this->getHelper('question');
            $question = new Question('Which name is you want to get:' . PHP_EOL);
            $name = $this->encryptAsk($helper, $question);
        }
        $user = $this->validPassword();
        $pass = PasswordForm::pass()->findPassword($user['id'], $name);
        if ($pass) {
            Password::toPasteDecode($pass, $this->_io);
        } else {
            $this->_io->error('You provide password is wrong or name is not exist!');
        }
    }

    protected function getOptionName(CompletionContext $context)
    {
        $names = PasswordForm::pass()->getDecryptedName();

        return $names;
    }
}