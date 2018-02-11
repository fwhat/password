<?php

namespace Dowte\Password\commands;

use Dowte\Password\pass\Password;
use Dowte\Password\pass\PasswordGenerate;
use Stecman\Component\Symfony\Console\BashCompletion\CompletionContext;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this->setName('generate')
            ->setAliases(['g'])
            ->setDescription('Generate a new password')
            ->setHelp('This command could help generate a new random password.')
            ->addOption('hidden', 'H', InputOption::VALUE_NONE, 'Whether or not to hidden the generate result.(max 100)')
            ->addOption('length', 'l', InputOption::VALUE_OPTIONAL, 'How length password you want generate', 12)
            ->addOption('level', 'L', InputOption::VALUE_OPTIONAL, 'Which password level generate', PasswordGenerate::LEVEL_THREE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hidden = $input->getOption('hidden');
        $length = $input->getOption('length');
        $level = $input->getOption('level');
        $genResult = PasswordGenerate::gen()->setLength($length)->setLevel($level)->get();
        if ($hidden === true) {
            $output->write($genResult);
        }
        Password::toPaste($genResult, $this->_io, '已复制在剪贴板');
    }

    protected function getOptionHidden(CompletionContext $context)
    {
        return ['t'];
    }

    protected function getOptionLevel(CompletionContext $context)
    {
        return PasswordGenerate::allLevel();
    }
}