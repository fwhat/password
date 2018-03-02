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
            ->setDescription('Generate a new random string')
            ->setHelp('This command could help generate a new random string.')
            ->addOption('no-hidden', 'H', InputOption::VALUE_NONE, 'Whether or not to hidden the generate result.')
            ->addOption('length', 'l', InputOption::VALUE_OPTIONAL, 'How length string you want generate(max 100)', 12)
            ->addOption('level', 'L', InputOption::VALUE_OPTIONAL, 'Which random string level to generate', PasswordGenerate::LEVEL_THREE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $noHidden = $input->getOption('no-hidden');
        $length = $input->getOption('length');
        $level = $input->getOption('level');
        $genResult = PasswordGenerate::gen()->setLength($length)->setLevel($level)->get();
        if ($noHidden === true) {
            $output->write($genResult);
        }
        Password::toPaste($genResult, $this->_io, '已复制在剪贴板');
    }

    protected function getOptionLevel(CompletionContext $context)
    {
        return PasswordGenerate::allLevel();
    }
}