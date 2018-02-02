<?php

namespace Dowte\Password\commands;


use Dowte\Password\forms\PasswordForm;
use Dowte\Password\pass\Password;
use Dowte\Password\pass\SymfonyAsk;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class PasswordCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('create-pass')

            // the short description shown while running "php bin/console list"
            ->setDescription('Creates a new password.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create a password...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = Password::askPassword($this, $input, $output);
        if ($user) {
            $helper = $this->getHelper('question');
            $question = new Question('Set a name for new password:' . PHP_EOL);
            $name = $helper->ask($input, $output, $question);

            $helper = $this->getHelper('question');
            $question = new Question('Set description for new password' . PHP_EOL);
            $description = $helper->ask($input, $output, $question);

            $helper = $this->getHelper('question');
            $question = new Question('Set the password' . PHP_EOL);
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            $password = Password::ask($helper, $input, $output, $question);

            PasswordForm::pass()->createPass($user['id'], $password, Password::encryptData($name), $description);
        }
    }
}