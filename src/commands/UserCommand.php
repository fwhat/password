<?php

namespace Dowte\Password\commands;

use Dowte\Password\forms\PasswordForm;
use Dowte\Password\forms\UserForm;
use Dowte\Password\pass\Password;
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
            ->addOption('username', 'u', InputOption::VALUE_OPTIONAL, 'New username for the Password')
            ->addOption('fix', 'f', InputOption::VALUE_NONE, 'Fix user-conf if miss the user-config')
            ->addOption('update-password', 'P', InputOption::VALUE_NONE, 'Update the master password');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userName = $input->getOption('username');
        $fix = $input->getOption('fix');

        //更新密码
        if ($input->getOption('update-password')) {
            $user = $this->validPassword();
            $passwords = ExportCommand::getDePasswords($user);
            $masterPassword = $this->askPassword('Set a new master password!');
            //用新的密钥重新加密
            $total = count($passwords);
            $i = 0;
            $this->_io->writeln('Encrypt passwords ----');
            foreach ($passwords as $password) {
                Password::processOutput(++$i, $total);
                PasswordForm::pass()->update(
                    $password['id'],
                    Password::encryptPasswordKey($password['keyword']),
                    Password::encryptPassword($masterPassword, $password['password']),
                    $password['description']);
            }
            UserForm::user()->update($user['id'], $masterPassword);
            Password::success('Update master password success!');
        }

        $askNameQuestion = $fix ? 'What\'s your old username?' : 'Set the username for Password?';
        if (! $userName) {
            $helper = $this->getHelper('question');
            $question = new Question($askNameQuestion);
            $userName = $helper->ask($input, $output, $question);
        }

        //重新生成本地user conf
        if ($fix) {
            $encryptName = Password::sha256($userName);
            if ($this->validPassword('', $encryptName)) {
                Password::userConfigure($encryptName);
            }
        } else {
            $password = $this->askPassword();
            if (! $password) {
                $this->_io->error('Password could\'t be empty');
            } else {
                $userName = UserForm::user()->createUser($userName, $password);
                Password::userConfigure($userName);
                $this->_io->success('User created !');
            }
        }
    }

    public function askPassword($questionMessage = 'Set the master password: ')
    {
        $helper = $this->getHelper('question');
        $question = new Question($questionMessage);
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $password = $this->sha256Ask($helper, $question);

        return $password;
    }
}