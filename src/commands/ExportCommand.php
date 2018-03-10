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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class ExportCommand extends Command
{
    const PASSWORD_YAML_DIR = __DIR__ . '/../../';

    protected function configure()
    {
        $this->setName('export')
            ->setDescription('Export your password library.')
            ->setHelp('This command help you to export your password library as a yaml file, you could use the file to import on other machine.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $this->validPassword();
        $passwords = self::getDePasswords($user, true);

        $file = FileUtil::realPath(self::PASSWORD_YAML_DIR . 'password-' . time() . '.yaml');
        file_put_contents($file, Yaml::dump(array_values($passwords)));

        $this->_io->success('Export success, detail see ' . $file);
    }

    /**
     * @param array $user the Command::ValidPassword return
     * @param bool $process
     * @return array
     */
    public static function getDePasswords($user, $process = false)
    {
        $passwords = PasswordForm::pass()->findModels(['id', 'keyword', 'password', 'description'], ['user_id' => $user['id']]);
        foreach ($passwords as $k => &$password) {
            ! $process or Password::processOutput($k + 1, count($passwords));
            $password['keyword'] = Password::decryptedPasswordKey($password['keyword']);
            $password['password'] = Password::decryptedPassword($user['password'], $password['password']);
        }

        return $passwords;
    }
}