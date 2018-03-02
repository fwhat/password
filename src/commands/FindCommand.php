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
            ->setDescription('Get a pass by name')
            ->setHelp('This command allows you to get a password...')
            ->addArgument('name', InputArgument::OPTIONAL, 'Get password from the password name.')
            ->addOption('list', 'a', InputOption::VALUE_NONE, 'Get all passwords of user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('list')) {
            $lists = PasswordForm::pass()->getDecryptedName("<fg=green>%s          </>");
            $output->writeln(trim($lists));
            return;
        }

        $name = $this->encryptArgument('name');
        $user = $this->validPassword();
        while (empty($name)) {
            $helper = $this->getHelper('question');
            $question = new Question('Which name is you want to get:' . PHP_EOL);
            $name = $this->encryptAsk($helper, $question);
        }
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