<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

namespace Dowte\Password\commands;

use Dowte\Password\forms\PasswordForm;
use Dowte\Password\forms\UserForm;
use Dowte\Password\pass\Password;
use Dowte\Password\pass\PasswordGenerate;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AlfredCommand extends Command
{
    protected $commands = ['generate'];

    protected function configure()
    {
        $this->setName('alfred')
            ->setDescription('Some Shortcut keys when use alfred')
            ->setHelp('This command is for user find password quickly when use alfred')
            ->addOption('init', '', InputOption::VALUE_NONE, 'Init the pass-alfred')
            ->addOption('keyword', 'k', InputOption::VALUE_OPTIONAL, 'Query password by keywords');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $query = $input->getOption('keyword');
        $init = $input->getOption('init');
        if ($init) {
            if (($user = $this->validPassword())) {
                $this->setPassword($user['password']);
                Password::success('Init pass-alfred success!');
            }
        }
        $masterPassword = $this->loadPassword();
        $passwordList = [
            "items" => []
        ];

        //alfred 其他命令
        if ($query == '-c') {
            foreach ($this->commands as $command) {
                $app = $this->getApplication()->find($command);
                $passwordList['items'][] = $this->alfredItems([
                    'title' => $app->getName(),
                    'subtitle' => $app->getDescription(),
                    'autoComplete' => $app->getName(),
                ]);
            }
        //执行可执行的命令
        } elseif (in_array($query, $this->commands)) {
            $app = $this->getApplication()->find($query);
            switch ($app->getName()) {
                case 'generate' :
                    $subTitle = 'Keydown cmd+enter copy new password to clipboard';
                    for ($i = 0; $i < 10; $i ++) {
                        $newPassword = Password::$pd->generate->get();
                        $passwordList['items'][] = $this->alfredItems([
                            'title' => $newPassword,
                            'subtitle' => $subTitle,
                            'autoComplete' => $app->getName(),
                            'cmdArg' => $newPassword,
                            'arg' => $newPassword,
                            'cmdSubtitle' => $subTitle,
                        ]);
                    }
            }

        } else {
            $user = UserForm::user()->findOne(['username' => Password::getUser()]);

            //todo use like
            $lists = PasswordForm::pass()->findModels(['keyword', 'description', 'password'], ['user_id' => $user['id']]);
            //构造alfred 返回格式
            foreach ($lists as $list) {
                $keyword = Password::decryptedPasswordKey($list['keyword']);
                $password = Password::decryptedPassword($masterPassword, $list['password']);
                if ($query) {
                    if (strstr($keyword , $query) === false ){
                        continue;
                    }
                }
                $passwordList['items'][] = $this->alfredItems([
                    'title' => $keyword,
                    'subtitle' => $list['description'],
                    'altSubtitle' => $list['description'],
                    'cmdSubtitle' => '**********',
                    'autoComplete' => $keyword,
                    'cmdArg' => $password,
                    'altArg' => $list['description'],
                    'arg' => $password
                ]);
            }
        }
        $this->_io->writeln(json_encode($passwordList));
    }

    /**
     * @param array $items ['key' => 'value']
     * @return array
     */
    protected function alfredItems($items = [])
    {
        $title = $subtitle = $autoComplete = $cmdArg = $cmdSubtitle = '';
        $icoPath = __DIR__ . '/../pass/icons/pass.ico';
        $cmdValid = true; $altValid = true;
        $altArg = $arg = $altSubtitle = '';
        $level = 0;
        foreach ($items as $k => $v) {
            $$k = $v;
        }

        $item = [
                    "title" => $title,
                    "subtitle" => $subtitle,
                    "arg" => $arg,
                    "icon" => [
                        "path" => realpath($icoPath)
                    ],
                    "autocomplete" => $autoComplete,
                    "mods" => [
                        "cmd" => [
                            "valid" => $cmdValid,
                            "arg" => $cmdArg,
                            "subtitle" => $cmdSubtitle
                        ],
                        "alt" => [
                            "valid" => $altValid,
                            "arg" => $altArg,
                            "subtitle" => $altSubtitle
                        ]
                    ],
                    "level" => $level
        ];
        return $item;
    }

    /**
     * create a readonly password file
     * @param $password
     */
    private function setPassword($password)
    {
        file_put_contents(ALFRED_CONF_FILE, $password);
        chmod(ALFRED_CONF_FILE, '0400');
    }

    private function loadPassword()
    {
        if (file_exists(ALFRED_CONF_FILE)) {
            $password = file_get_contents(ALFRED_CONF_FILE);
            if ($this->validPassword($password)) {
                return $password;
            }
        }
        Password::error('Miss password or password is wrong, please init alfredCommand before use alfred.');
    }
}