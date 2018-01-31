<?php

namespace Dowte\Password\controllers;


use Dowte\Password\forms\PasswordForm;
use Dowte\Password\pass\Password;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('list')

            // the short description shown while running "php bin/console list"
            ->setDescription('Get password list.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to get password list...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Password::askPassword($this, $input, $output);
        $names = PasswordForm::pass()->findModels('name');
        $lists = '';
        foreach ($names as $name) {
            $lists .= '<fg=green>' . $name['name'] . '</>' . PHP_EOL;
        }
        $output->writeln(trim($lists));
    }

}