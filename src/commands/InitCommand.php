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
            ->addOption('way', 'w', InputOption::VALUE_OPTIONAL, 'Which way for save password records.')
            ->addOption('no-db', 'd', InputOption::VALUE_NONE, 'No DB config ask.')
            ->addOption('default', null, InputOption::VALUE_NONE, 'Default configure');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $ways = PasswordDb::ways();
        $way = $input->getOption('way');
        $default = $input->getOption('default');
        if (is_numeric($way) && isset($ways[$way])) {
            $way = $ways[$way];
        }
        $noDb = $input->getOption('no-db');
        $db = new PasswordDb();
        if (! $noDb) {
            if (empty($way) || ! in_array($way, $ways)) {
                $question = new ChoiceQuestion(
                    '请选择储存密码文件的方式 (默认0)',
                    $db::ways(),
                    0
                );
                $question->setErrorMessage('the way %s is invalid.');
                $way = $helper->ask($input, $output, $question);
            }

        } else {
            $way = $db->getDbWay();
        }
        $noDb or $this->configureDb($way, $default);
        $db->setWay($way)->dbInit();
        $status = $this->dumpCompletion();
        if ($status) {
            $this->_io->success('DbInit Success !');

        } else {
            $this->_io->error('DbInit Error !');
        }
    }

    protected function configureDb($way, $default = false)
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
        if ($default) {
            $config['components']['db'] = array_merge($config['components']['db'], $this->defaultConfigs()[$way]);
        } else {
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

    protected function defaultConfigs()
    {
        return [
            'mysql' => [
                'DB_DSN' => 'mysql:host=127.0.0.1;dbname=password',
                'DB_USER' => 'root',
                'DB_PASS' => ''
            ],
            'sqlite' => [
                'DB_DIR' => DB_DIR,
                'DB_NAME' => 'password'
            ],
            'yamlFile' => [
                'DB_DIR' => DB_DIR,
            ]
        ];
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