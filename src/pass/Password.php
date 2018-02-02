<?php
namespace Dowte\Password\pass;

use Dowte\Password\forms\UserForm;
use Dowte\Password\pass\db\ActiveRecordInterface;
use Dowte\Password\pass\db\ConnectionInterface;
use Dowte\Password\pass\db\DbClear;
use Dowte\Password\pass\db\DbInit;
use Dowte\Password\pass\db\DbInitInterface;
use Dowte\Password\pass\exceptions\UserException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class Password
{
    const BASE_NAMESPACE = 'Dowte\Password\\';

    public static $dbInitNamespace = 'Dowte\Pssword\pass\db\\';

    public static $dbInitClass = 'DbInit';

    public static $params = [];

    /**
     * @var ActiveRecordInterface
     */
    public static $dbClass;

    public static $dbConfig;

    public function __construct($options = [])
    {
        foreach ($options as $name => $value){
            if ($name === 'components') {
                $this->loadComponents($value);
                continue;
            }
            if ($name === 'params') {
                $this->loadParams($value);
            }
        }
    }

    protected function loadParams(array $value)
    {
        foreach ((array) $value as $name => $item) {
            self::$params[$name] = $item;
        }
    }

    protected function loadComponents(array $configs)
    {
        foreach ((array) $configs as $name => $config) {
            if ($name === 'db') {
                if (isset($config['class']) && class_exists(self::BASE_NAMESPACE . $config['class'])) {
                    $className = self::BASE_NAMESPACE . $config['class'];
                    $instance = new $className();
                    if ($instance instanceof ConnectionInterface) {
                        return $instance->init($config);
                    }
                    die('Connection db error!');
                } else {
                    die('Connection db error!');
                }
            }
        }
    }

    public static function getUserConfFile()
    {
        return PASS_USER_CONF_DIR . '.user';
    }


    public static function getUser()
    {
        $user = @file_get_contents(self::getUserConfFile());
        if (! $user) {
            throw new UserException('Please create user at first! ');
        }
        return $user;
    }

    public static function userConf($userName)
    {
        $filename = self::getUserConfFile();
        file_put_contents($filename, $userName);
        return chmod($filename, 0400);
    }

    /**
     * @param $command \Dowte\Password\commands\Command
     * @param $input
     * @param OutputInterface $output
     * @return array|null
     */
    public static function askPassword($command, $input, OutputInterface $output)
    {
        $helper = $command->getHelper('question');
        $question = new Question('What is the database password?');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $password = self::ask($helper, $input, $output, $question);
        $user = UserForm::user()->findUser(Password::getUser(), $password);
        if (! $user) {
            $command->getIO()->error('Please check the password is right!');
        } else {
            return $user;
        }
    }

    /**
     * @param $password
     * @param $validPassword
     * @return bool
     */
    public static function validPassword($password, $validPassword)
    {
        openssl_private_decrypt(base64_decode($password), $decrypted, self::getPrivateKey());
        openssl_private_decrypt(base64_decode($validPassword), $validDecrypted, self::getPrivateKey());
        if ($decrypted === $validDecrypted) {
            return true;
        }
        return false;
    }

    /**
     * @param $data
     * @return mixed
     */
    public static function encryptData($data)
    {
        openssl_public_encrypt($data, $encrypted, self::getPublicKey());
        return base64_encode($encrypted);
    }

    /**
     * @param $data
     * @return mixed
     */
    public static function decryptedData($data)
    {
        openssl_private_decrypt(base64_decode($data), $decrypted, self::getPrivateKey());
        return $decrypted;
    }

    public static function getPublicKey()
    {
        return file_get_contents(self::$params['public_key']);
    }

    public static function getPrivateKey()
    {
        return file_get_contents(self::$params['private_key']);
    }

    public static function ways()
    {
        return DbInit::ways();
    }

    public static function dbInit($way)
    {
        return ((new DbInit())->setWay($way))->exec();
    }

    public static function clear()
    {
        //clear db
        (new DbClear())->exec();
        //clear user file
        return unlink(self::getUserConfFile());
    }

    /**
     * @param $messages
     * @param $io SymfonyStyle
     */
    public static function writePaste($messages, $io)
    {
        $messages = Password::decryptedData($messages);
        $status = self::copy($messages);
        if ($status) {
            $io->success('复制剪贴板成功 !');
        } else {
            $io->error('复制剪贴板失败 !');
        }
    }

    protected static function copy($messages)
    {
        return shell_exec('echo "'. $messages. '" | pbcopy');
    }

    public static function ask(QuestionHelper $helper, InputInterface $input, OutputInterface $output, Question $question)
    {
        $messages = $helper->ask($input, $output, $question);
        return self::encryptData($messages);
    }
}