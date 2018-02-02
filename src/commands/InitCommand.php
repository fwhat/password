<?php

namespace Dowte\Password\commands;

use Dowte\Password\pass\Password;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class InitCommand extends Command
{
    protected function configure()
    {
        $this->setName('init')
            ->setDescription('DbInit pass-cli settings');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            '请选择储存密码文件的方式 (默认本地)',
            Password::ways(),
            0
        );
        $question->setErrorMessage('the way %s is invalid.');
        $way = $helper->ask($input, $output, $question);
        Password::dbInit($way);
        $status = $this->dumpCompletion('/usr/local/bin/pass');
        if ($status) {
            $this->_io->success('DbInit Success !');

        } else {
            $this->_io->error('DbInit Error !');
        }
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