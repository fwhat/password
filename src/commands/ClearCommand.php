<?php

namespace Dowte\Password\commands;


use Dowte\Password\pass\PasswordDb;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ClearCommand extends Command
{
    protected function configure()
    {
        $this->setName('clear')
            ->setDescription('Clear your password data !')
            ->setHelp('This command allows you to clear db data, ' . PHP_EOL . 'And if you want use Application after clear, you need init again.')
            ->addOption('full', 'f', InputOption::VALUE_NONE, 'Clear the whole data file!');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(
            'Warning, this command will clear your password records, Y/N ?',
            'false');
        $way = $helper->ask($input, $output, $question);
        if ($way !== true) {
            return;
        }
        $this->validPassword();
        $pdb = new PasswordDb();
        $way = $pdb->getDbWay();
        $pdb->setWay($way);

        if ($input->getOption('full')) {
            $pdb->clear();
            $this->_io->success('Clear password data success!');
        } else {
            $pdb->clearDb();
            $this->_io->success('Clear password db data success!');
        }
    }
}