<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

namespace Dowte\Password\commands;

use Dowte\Password\pass\db\BaseConnection;
use Dowte\Password\pass\Password;
use Dowte\Password\pass\PasswordDb;
use Stecman\Component\Symfony\Console\BashCompletion\CompletionContext;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;

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
        $this->configureDb($way);
        $db->setWay($way)->dbInit();
        $status = $this->dumpCompletion();
        if ($status) {
            $this->_io->success('DbInit Success !');

        } else {
            $this->_io->error('DbInit Error !');
        }
    }

    protected function configureDb($way)
    {
        $className = sprintf("pass\db\%s\Connection", $way);
        /** @var BaseConnection $class*/
        $class = Password::BASE_NAMESPACE . $className;
        $config = [
            'components' => [
                'db' => [
                    'class' => $className
                ]
            ]
        ];
        $requires = $class::requireProperties();
        $helper = $this->getHelper('question');
        foreach ($requires as $require) {
            $mainMessage = sprintf("set %s %s ", $way, $require);
            if (defined($require)) {
                $questionMessage = $mainMessage . sprintf("(default value %s)", constant($require));
                $question = new Question($questionMessage . PHP_EOL);
                $answer = $helper->ask($this->_input, $this->_output, $question);
                $config['components']['db'][$require] = $answer ?: constant($require);
            } else {
                do {
                    $questionMessage = $mainMessage . sprintf("(%s is required)", $require);
                    $question = new Question($questionMessage . PHP_EOL);
                    $answer = $helper->ask($this->_input, $this->_output, $question);
                } while (!$answer);
                $config['components']['db'][$require] = $answer;
            }
        }
        $oldConfig = [];
        if (file_exists(CONF_FILE)) {
            $oldConfig = Yaml::parseFile(CONF_FILE);
        }
        $newConfig = array_merge($oldConfig, $config);
        file_put_contents(CONF_FILE, Yaml::dump($newConfig));
        Password::init($newConfig);
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