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
            $name = $input->getOption('name');
            if (! $name) {
                $helper = $this->getHelper('question');
                $question = new Question('Which name is you want to get:' . PHP_EOL);
                $name = $helper->ask($input, $output, $question);
            }
            $this->validPassword();
            $pass = PasswordForm::pass()->findOne(['name' => $name], ['password']);
            if (! $pass || !isset($pass['password'])) {
                $this->_io->error('You provide password is wrong or name is not exist!');
            } else {
                Password::toPasteDecode($pass['password'], $this->_io);
            }
    }

    protected function getOptionPass(CompletionContext $context)
    {
        $names = PasswordForm::pass()->getDecryptedName();

        return $names;
    }
}