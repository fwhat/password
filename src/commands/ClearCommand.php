<?php

namespace Dowte\Password\commands;


use Dowte\Password\pass\Password;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ClearCommand extends Command
{
    protected function configure()
    {
        $this->setName('clear')
            ->setDescription('Clear your password data !')
            ->setHelp('This command allows you to clear data, ' . PHP_EOL . 'And if you want use Application after clear, you need init again.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(
            '<error>Warning, this command will clear your password records, Y/N ?</error>',
            'false');
        $way = $helper->ask($input, $output, $question);
        if ($way !== true) {
            return;
        }
        if (Password::askPassword($this, $input, $output)) {
            Password::clear();
            $this->_io->success('Clear password data success!');
        }
    }
}