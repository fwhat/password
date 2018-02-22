<?php

namespace Dowte\Password\commands;

use Dowte\Password\pass\PassSecret;
use Dowte\Password\pass\Password;
use Stecman\Component\Symfony\Console\BashCompletion\CompletionContext;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class InitCommand extends Command
{
    protected function configure()
    {
        $this->setName('init')
            ->setDescription('DbInit pass-cli settings')
            ->setHelp('This command could help you init this application! ')
            ->addOption('way', 'w', InputOption::VALUE_OPTIONAL, 'Which way for save password records.')
            ->addOption('generate-secret', 'G', InputOption::VALUE_NONE, 'Generate new openssl secret keys.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $way = $input->getOption('way');
        if (empty($way) || ! in_array($way, Password::ways())) {
            $question = new ChoiceQuestion(
                '请选择储存密码文件的方式 (默认1)',
                Password::ways(),
                1
            );
            $question->setErrorMessage('the way %s is invalid.');
            $way = $helper->ask($input, $output, $question);
        }
        if ($input->getOption('generate-secret')) {
            $secret = new PassSecret();
            $secretKeyDir = __DIR__ . '/../../data/';
            $secret->setSecretKeyDir($secretKeyDir);
            if ($secret->buildSecretKey()) {
                $this->_io->success('Generate new keys success, the keys is save in ' . realpath($secretKeyDir));
            }
        }
        Password::dbInit($way);
        $status = $this->dumpCompletion('/usr/local/bin/pass');
        if ($status) {
            $this->_io->success('DbInit Success !');

        } else {
            $this->_io->error('DbInit Error !');
        }
    }

    protected function getOptionWay(CompletionContext $context)
    {
        return Password::ways();
    }

    private function dumpCompletion($program)
    {
        if (empty($program)) {
            $program = $_SERVER['argv'][0];
        }

        $command = $this->getApplication()->find('_completion');
        $arguments = [
            'command' => '_completion',
            '--generate-hook'  => true,
            '--program'    => $program,
        ];

        $buffer = new BufferedOutput();
        $completionInput = new ArrayInput($arguments);
        $command->run($completionInput, $buffer);
        $completion = $buffer->fetch();
        $status = file_put_contents('./pass-cli.bash', $completion);
        return $status;
    }
}