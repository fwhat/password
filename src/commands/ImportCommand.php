<?php

namespace Dowte\Password\commands;

use Dowte\Password\forms\PasswordForm;
use Dowte\Password\pass\components\FileUtil;
use Dowte\Password\pass\PassSecret;
use Dowte\Password\pass\Password;
use Stecman\Component\Symfony\Console\BashCompletion\CompletionContext;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class ImportCommand extends Command
{
    protected function configure()
    {
        $this->setName('import')
            ->setDescription('Import passwords from yaml file')
            ->setHelp('This command help you to import passwords displace one by one set into')
            ->addArgument('file', InputArgument::REQUIRED, 'A file use to import');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $this->validPassword();
        $file = $input->getArgument('file');
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'yaml') {
            $this->_io->error('Please provide yaml file');
        }
        if (file_exists(exec('pwd') . $file)) {
            $file = exec('pwd') . $file;
        }
        if (! file_exists($file)) {
            $this->_io->error('The file is not exists');
        }

        $passwords = Yaml::parseFile($file);
        $this->validImportData($passwords);
        foreach ($passwords as $password) {
            PasswordForm::pass()->createPass(
                $user['id'],
                PassSecret::encryptData($password['password']),
                Password::encryptPasswordKey($password['keyword']),
                (isset($password['description']) ? $password['description'] : ''));
        }
        $this->_io->success('Import password success!');
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