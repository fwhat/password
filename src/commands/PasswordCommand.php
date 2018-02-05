<?php

namespace Dowte\Password\commands;


use Dowte\Password\forms\PasswordForm;
use Dowte\Password\pass\PassSecret;
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
        $user = $this->validPassword();
        $name = '';
        $password = '';
        if ($user) {
            while (empty($name)) {
                $helper = $this->getHelper('question');
                $question = new Question('Set a name for new password: (name is required)' . PHP_EOL);
                $name = $helper->ask($input, $output, $question);
            }
            while (empty($password)) {
                $helper = $this->getHelper('question');
                $question = new Question('Set the password: (password is required)' . PHP_EOL);
                $question->setHidden(true);
                $question->setHiddenFallback(false);
                $password = $this->encryptAsk($helper, $question);
            }

            $helper = $this->getHelper('question');
            $question = new Question('Set description for new password' . PHP_EOL);
            $description = $helper->ask($input, $output, $question);

            $status = PasswordForm::pass()->createPass($user['id'], $password, PassSecret::encryptData($name), $description);
            if ($status) {
                $this->_io->success('Create new password record success!');
            } else {
                $this->_io->error('Create new password record false!');
            }
        }
    }
}