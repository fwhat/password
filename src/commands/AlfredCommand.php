<?php

namespace Dowte\Password\commands;

use Dowte\Password\forms\PasswordForm;
use Dowte\Password\forms\UserForm;
use Dowte\Password\pass\PassSecret;
use Dowte\Password\pass\Password;
use Dowte\Password\pass\PasswordGenerate;
use Symfony\Component\Console\Input\InputArgument;
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
            ->addArgument('action', InputArgument::OPTIONAL)
            ->addOption('keyword', 'k', InputOption::VALUE_OPTIONAL, 'Query password by keywords');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $query = $input->getOption('keyword');
        $action = $input->getArgument('action');
        if ($action === 'init') {
            if (($user = $this->validPassword())) {
                $this->setPassword($user['password']);
                Password::success('Init alfred success!');
            }
        }
        $this->loadPassword();
        $passwordList = [
            "items" => []
        ];
        if ($query == '-c') {
            foreach ($this->commands as $command) {
                $app = $this->getApplication()->find($command);
                $passwordList['items'][] = $this->alfredItems($app->getName(), $app->getDescription(), $app->getName());
            }

        } elseif (in_array($query, $this->commands)) {
            $app = $this->getApplication()->find($query);
            switch ($app->getName()) {
                case 'generate' :
                    $subTitle = 'Keydown cmd+enter copy new password to clipboard';
                    for ($i = 0; $i < 10; $i ++) {
                        $newPassword = PasswordGenerate::gen()->get();
                        $passwordList['items'][] = $this->alfredItems($newPassword, $subTitle, $app->getName(), $newPassword, '', $subTitle);
                    }
            }

        } else {
            $user = UserForm::user()->findOne(['username' => Password::getUser()]);

            $lists = PasswordForm::pass()->findModels(['name', 'description', 'password'], ['user_id' => $user['id']]);
            foreach ($lists as $list) {
                $name = PassSecret::decryptedData($list['name']);
                $password = PassSecret::decryptedData($list['password']);
                if ($query) {
                    if (strstr($name , $query) === false ){
                        continue;
                    }
                }
                $passwordList['items'][] = $this->alfredItems($name, $list['description'], $name, $password);
            }
        }
        $this->_io->writeln(json_encode($passwordList));
    }

    protected function alfredItems($title = '', $subtitle = '', $autoComplete = '', $cmdArg = '', $arg = '', $cmdSubtitle = '', $icoPath = __DIR__ . '/../pass/icons/pass.ico',
        $cmdValid = true, $altValid = true, $altArg = '', $altSubtitle = '', $level = 0)
    {
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

    private function setPassword($password)
    {
        if (! file_exists(ALFRED_CONF_FILE)) {
            $fp = fopen(ALFRED_CONF_FILE, 'r+');
            fclose($fp);
        }
        file_put_contents(ALFRED_CONF_FILE, $password);
        chmod(ALFRED_CONF_FILE, '0400');
    }

    private function loadPassword()
    {
        if (file_exists(ALFRED_CONF_FILE)) {
            $password = file_get_contents(ALFRED_CONF_FILE);
            if ($this->validPassword($password)) {
                return true;
            }
        }
        Password::error('Miss password or password is wrong, please init alfredCommand before use alfred.');
    }
}