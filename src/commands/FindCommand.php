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

class FindCommand extends Command
{
    protected function configure()
    {
        $this->setName('find')
            ->setDescription('Get a password by keyword')
            ->setHelp('This command allows you to get a password...')
            ->addArgument('keyword', InputArgument::OPTIONAL, 'Get password by the password keyword.')
            ->addOption('list', 'a', InputOption::VALUE_NONE, 'Get all passwords of keyword.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('list')) {
            $lists = PasswordForm::pass()->getDecryptedKey("<fg=green>%s          </>");
            $output->writeln(trim($lists));
            return;
        }

        $key = $this->encryptArgument('keyword');
        $user = $this->validPassword();
        while (empty($key)) {
            $helper = $this->getHelper('question');
            $question = new Question('Which password is you want to get:' . PHP_EOL);
            $key = $this->encryptAsk($helper, $question);
        }
        $pass = PasswordForm::pass()->findPassword($user['id'], $key);
        if ($pass) {
            Password::toPasteDecode($pass, $this->_io);
        } else {
            $this->_io->error('You provide password is wrong or keyword is not exist!');
        }
    }

    protected function getOptionName(CompletionContext $context)
    {
        $keys = PasswordForm::pass()->getDecryptedKey();

        return $keys;
    }
}