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
use Dowte\Password\pass\PasswordDb;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class CleanCommand extends Command
{
    protected function configure()
    {
        $this->setName('clean')
            ->setAliases(['clear'])//to be compatibility
            ->setDescription('Clean your password data !')
            ->setHelp('This command allows you to clean db data, ' . PHP_EOL . 'And if you want use Application after clean, you need init again.')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Clean the whole config and DB records!');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(
            'Warning, this command will clean your password records, Y/N ?',
            'false');
        $way = $helper->ask($input, $output, $question);
        if ($way !== true) {
            return;
        }
        $user = $this->validPassword();
        $pdb = new PasswordDb();
        $pdb->setWay($pdb->getDbWay());

        if ($input->getOption('all')) {
            //有其他使用者时不允许清空库
            if ($this->checkCleanDb()) {
                $pdb->clean($user);
                $this->_io->success('Clean password data success!');
            } else {
                $this->_io->error('The database has more then one owner, can not be clean!');
            }
        } else {
            $pdb->dbClean($user);
            $this->_io->success('Clean password db data success!');
        }
    }

    protected function checkCleanDb()
    {
        $passwords = PasswordForm::pass()->findModels(['*']);
        $users = [];
        foreach ($passwords as $password) {
            $users[$password['user_id']] = true;
        }

        return count($users) <= 1 ? true : false;
    }
}