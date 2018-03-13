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
use Dowte\Password\pass\Password;
use Dowte\Password\pass\PasswordGenerate;
use Stecman\Component\Symfony\Console\BashCompletion\CompletionContext;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class PasswordCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('password')
            ->setAliases(['p'])

            // the short description shown while running "php bin/console list"
            ->setDescription('Create or update or delete password.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create|update|create a password...')
            ->addOption('keyword', 'k', InputOption::VALUE_OPTIONAL, 'The keyword for password')
            ->addOption('exec', 'e', InputOption::VALUE_OPTIONAL, 'Choose update(u)|delete(d)|create(c), default create(c)', 'c')
            ->addOption('no-description', 'D', InputOption::VALUE_NONE, 'Don\'t set description for new password')
            ->addOption('generate', 'g', InputOption::VALUE_NONE, 'Generate a random string for new password(level 3 length 12)')
            ->addOption('length', 'l', InputOption::VALUE_OPTIONAL, 'How length random string you want generate.(max 100)', 12)
            ->addOption('level', 'L', InputOption::VALUE_OPTIONAL, 'Which random string level to generate', PasswordGenerate::LEVEL_THREE)
            ->addOption('all', null, InputOption::VALUE_NONE, 'Delete all passwords when user --exec=delete command');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $this->validPassword();
        $keyword = $input->getOption('keyword');
        $noDescription = $input->getOption('no-description');
        $generate = $input->getOption('generate');
        $length = $input->getOption('length');
        $level = $input->getOption('level');
        $exec = $input->getOption('exec');
        $all = $input->getOption('all');
        $description = '';
        $newPassword = $generate === true ? Password::$pd->generate->setLength($length)->setLevel($level)->get() : '';

        //获取必要信息
        if ($newPassword) {
            $password = Password::encryptPassword($user['password'], $newPassword);
            Password::toPaste($newPassword, $this->_io, 'The new password is set into clipboard.');
        }
        if ($all && ($exec == 'delete' || $exec == 'd')) {
            if (PasswordForm::pass()->deleteByConditions(['user_id' => $user['id']]) !== false) {
                Password::success('Delete all success!');
            }
        }
        while (empty($keyword)) {
            $helper = $this->getHelper('question');
            $question = new Question('The keyword for password: (keyword is required for search)' . PHP_EOL);
            $keyword = $helper->ask($input, $output, $question);
        }
        $enKeyword = Password::encryptPasswordKey($keyword);
        $model = PasswordForm::pass()->findOne(['keyword' => $enKeyword]);

        //delete
        if ($exec == 'delete' || $exec == 'd') {
            if (!$model) {
                Password::error('The keyword is not exists!');
            }
            if (PasswordForm::pass()->delete($model['id'])) {
                Password::success('Delete ' . $keyword . ' success!');
            }
            Password::error('Delete ' . $keyword . ' error!');
        }

        while (empty($password)) {
            $helper = $this->getHelper('question');
            $question = new Question('The password: (password is required)' . PHP_EOL);
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            $password = Password::encryptPassword($user['password'], $helper->ask($input, $output, $question));
        }

        if ($noDescription !== true) {
            $helper = $this->getHelper('question');
            $question = new Question('The description for password' . PHP_EOL);
            $description = $helper->ask($input, $output, $question);
        }
        //update
        if ($exec == 'update' || $exec == 'u') {
            if (!$model) {
                Password::error('The keyword is not exists!');
            }
            if (PasswordForm::pass()->update($model['id'], $enKeyword, $password, $description)) {
                Password::success('Update ' . $keyword . ' success!');
            }
            Password::error('Update ' . $keyword . ' error!');
        }

        //create
        if ($model) {
            Password::error('This keyword item is already exists!');
        }
        $status = PasswordForm::pass()->createPass($user['id'], $password, $enKeyword, $description);
        if ($status) {
            $this->_io->success('Create new password record success!');
        } else {
            $this->_io->error('Create new password record false!');
        }
    }

    public function getOptionKeyword(CompletionContext $context)
    {
        $lists = PasswordForm::pass()->getDecryptedKey();
        return $lists;
    }
}