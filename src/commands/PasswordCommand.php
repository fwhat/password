<?php

namespace Dowte\Password\commands;


use Dowte\Password\forms\PasswordForm;
use Dowte\Password\pass\Password;
use Dowte\Password\pass\PasswordGenerate;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class PasswordCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('password')
            ->setAliases(['p'])

            // the short description shown while running "php bin/console list"
            ->setDescription('Creates a new password.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create a password...')
            ->addOption('keyword', 'k', InputOption::VALUE_OPTIONAL, 'Set a keyword for new password')
            ->addOption('description', 'd', InputOption::VALUE_OPTIONAL, 'Set a description for new password')
            ->addOption('no-description', 'D', InputOption::VALUE_NONE, 'Don\'t set description for new password')
            ->addOption('generate', 'g', InputOption::VALUE_NONE, 'Generate a random string for new password(level 3 length 12)')
            ->addOption('no-hidden', 'H', InputOption::VALUE_NONE, 'Whether or not to hidden the generate result.')
            ->addOption('length', 'l', InputOption::VALUE_OPTIONAL, 'How length random string you want generate.(max 100)', 12)
            ->addOption('level', 'L', InputOption::VALUE_OPTIONAL, 'Which random string level to generate', PasswordGenerate::LEVEL_THREE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $this->validPassword();
        $keyword = $input->getOption('keyword');
        $description = $input->getOption('description');
        $noDescription = $input->getOption('no-description');
        $generate = $input->getOption('generate');
        $length = $input->getOption('length');
        $level = $input->getOption('level');
        $hidden = $input->getOption('no-hidden');
        $newPassword = $generate === true ? PasswordGenerate::gen()->setLength($length)->setLevel($level)->get() : '';
        if ($newPassword) {
            $password = Password::encryptPassword($user['password'], $newPassword);
            Password::toPaste($newPassword, $this->_io, 'The new password is set into clipboard.');
            if ($hidden) {
                $this->_io->success('The new password is' . $newPassword);
            }
        }
        if ($user) {
            while (empty($keyword)) {
                $helper = $this->getHelper('question');
                $question = new Question('Set a keyword for new password: (keyword is required for search)' . PHP_EOL);
                $keyword = $helper->ask($input, $output, $question);
            }
            while (empty($password)) {
                $helper = $this->getHelper('question');
                $question = new Question('Set the password: (password is required)' . PHP_EOL);
                $question->setHidden(true);
                $question->setHiddenFallback(false);
                $password = Password::encryptPassword($user['password'], $helper->ask($input, $output, $question));
            }

            if ($noDescription !== true && ! $description) {
                $helper = $this->getHelper('question');
                $question = new Question('Set description for new password' . PHP_EOL);
                $description = $helper->ask($input, $output, $question);
            }

            $status = PasswordForm::pass()->createPass($user['id'], $password, Password::encryptPasswordKey($keyword), $description);
            if ($status) {
                $this->_io->success('Create new password record success!');
            } else {
                $this->_io->error('Create new password record false!');
            }
        }
    }
}