<?php

namespace Dowte\Password\commands;

use Dowte\Password\pass\Password;
use Dowte\Password\pass\PasswordDb;
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
            ->setDescription('DbInit pass settings')
            ->setHelp('This command could help you init this application! ')
            ->addOption('way', 'w', InputOption::VALUE_OPTIONAL, 'Which way for save password records.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $way = $input->getOption('way');
        $db = new PasswordDb();
        if (empty($way) || ! in_array($way, PasswordDb::ways())) {
            $question = new ChoiceQuestion(
                '请选择储存密码文件的方式 (默认0)',
                $db::ways(),
                0
            );
            $question->setErrorMessage('the way %s is invalid.');
            $way = $helper->ask($input, $output, $question);
        }
        $db->setWay($way)->init();
        $status = $this->dumpCompletion();
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

    /**
     * 生成自动补全
     * @param string $program
     * @return bool|int
     */
    private function dumpCompletion($program = '')
    {
        if (empty($program)) {
            $program = Password::getCommandPath($_SERVER['argv'][0]);
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
        $status = file_put_contents(__DIR__ . '/../../pass-cli.bash', $completion);
        return $status;
    }
}