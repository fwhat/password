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
        $total = count($passwords);
        $i = 0;
        foreach ($passwords as $password) {
            Password::processOutput(++$i, $total);
            //如果keyword 存在则跳过
            $enKeyword = Password::encryptPasswordKey($password['keyword']);
            if (($model = PasswordForm::pass()->findOne(['keyword' => $enKeyword]))) {
                //是否覆盖存在项
                if ($overwrite) {
                    PasswordForm::pass()->update(
                        $model['id'],
                        $enKeyword,
                        Password::encryptPassword($user['password'], $password['password']),
                        (isset($password['description']) ? $password['description'] : '')
                    );
                    $successCount ++;
                    continue;
                } else {
                    $skipArr[] = $password['keyword'];
                    continue;
                }
            }
            PasswordForm::pass()->createPass(
                $user['id'],
                Password::encryptPassword($user['password'], $password['password']),
                $enKeyword,
                (isset($password['description']) ? $password['description'] : ''));
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

    private function validImportData($passwords)
    {
        foreach ($passwords as $password) {
            if (! isset($password['keyword']) || ! isset($password['password'])) {
                $this->_io->error('The password yaml file format error!');
            }
        }
    }
}