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
use Dowte\Password\pass\components\FileUtil;
use Dowte\Password\pass\Password;
use Stecman\Component\Symfony\Console\BashCompletion\CompletionContext;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class ImportCommand extends Command
{
    protected function configure()
    {
        $this->setName('import')
            ->setDescription('Import passwords from yaml file')
            ->setHelp('This command help you to import passwords displace one by one set into')
            ->addArgument('file', InputArgument::REQUIRED, 'A file use to import')
            ->addOption('overwrite', null, InputOption::VALUE_NONE, 'Overwrite password item if keyword exists!');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $this->validPassword();
        $file = $input->getArgument('file');
        $overwrite = $input->getOption('overwrite');
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'yaml') {
            $this->_io->error('Please provide yaml file');
        }
        if (file_exists(exec('pwd') . $file)) {
            $file = exec('pwd') . $file;
        }
        if (! file_exists($file)) {
            Password::error('The import file is not exists');
        }

        $passwords = Yaml::parseFile($file);
        $this->validImportData($passwords);
        $successCount = 0;
        $skipArr = [];
        $i = 0;
        $passwordItems = $this->getClassifyPasswords($passwords, $user);
        $total = $overwrite ? count($passwords) : count($passwordItems['no_exists']);

        //覆盖存在项  todo more fast
        if ($overwrite) {
            foreach ($passwordItems['exists'] as $item) {
                Password::processOutput(++$i, $total);
                PasswordForm::pass()->update(
                    $item['id'],
                    $item['keyword'],
                    Password::encryptPassword($user['password'], $item['password']),
                    (isset($item['description']) ? $item['description'] : '')
                );
                $successCount ++;
            }
        } else {
            $skipArr = array_keys($passwordItems['exists']);
        }

        //插入
        foreach ($passwordItems['no_exists'] as $item) {
            Password::processOutput(++$i, $total);
            PasswordForm::pass()->createPass(
                $user['id'],
                Password::encryptPassword($user['password'], $item['password']),
                $item['keyword'],
                (isset($item['description']) ? $item['description'] : ''));
            $successCount ++;
        }
        $message = 'Import password success! ';
        if ($skipArr) {
            $message .= sprintf("success %d skip %d\n", $successCount, count($skipArr));
            $message .= sprintf("skip info : \n -- %s", implode(PHP_EOL . ' -- ', $skipArr));
        }
        $this->_io->success($message);
    }

    protected function getArgumentFile(CompletionContext $context)
    {
        return FileUtil::getFiles(exec('pwd'), 'yaml');
    }

    protected function getClassifyPasswords($passwords, $user)
    {
        $item = ['exists' => [], 'no_exists' => []];
        $models = PasswordForm::pass()->findModels('*', ['user_id' => $user['id']]);
        $newPassword = $oldKeywords= [];
        foreach ($passwords as $password) {
            $newPassword[$password['keyword']] = $password;
        }
        foreach ($models as $model) {
            $oldKeywords[$model['keyword']] = $model;
        }
        foreach ($newPassword as $password) {
            $password['keyword'] = Password::encryptPasswordKey($password['keyword']);
            if (isset($oldKeywords[$password['keyword']])) {
                $item['exists'][$password['keyword']] = array_merge($password, ['id' => $oldKeywords[$password['keyword']]['id']]);
            } else {
                $item['no_exists'][$password['keyword']] = $password;
            }
        }

        return $item;
    }

    private function validImportData($passwords)
    {
        foreach ($passwords as $password) {
            if (! isset($password['keyword']) || ! isset($password['password'])) {
                $this->_io->error('The password yaml file format error!');
            }
        }
    }

}