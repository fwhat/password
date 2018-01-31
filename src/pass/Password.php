<?php
namespace Dowte\Password\pass;

use Dowte\Password\forms\UserForm;
use Dowte\Password\pass\db\ActiveRecordInterface;
use Dowte\Password\pass\exceptions\UserException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;

class Password
{
    const BASE_NAMESPACE = 'Dowte\Password\\';

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
            self::$params[$name] = $value;
        }
    }

    protected function loadComponents($configs)
    {
        foreach ($configs as $name => $config) {
            if ($name === 'db') {
                if (isset($config['class'])) {
                    (self::BASE_NAMESPACE . $config['class'])::init($config);

                } else {
                    die('Connection db error!');
                }
            }
        }
    }

    public static function getUser()
    {
        defined(PASS_USER_CONF_DIR) or define(PASS_USER_CONF_DIR, __DIR__ . '/../../data/');

        $user = file_get_contents(PASS_USER_CONF_DIR . 'user.conf');
        if (! $user) {
            throw new UserException('Please create user at first! ');
        }
        return $user;
    }

    public static function userConf($userName)
    {
        defined(PASS_USER_CONF_DIR) or define(PASS_USER_CONF_DIR, __DIR__ . '/../../data/');

        file_put_contents(PASS_USER_CONF_DIR . 'user.conf', $userName);
    }

    /**
     * @param $command Command
     * @param $input
     * @param $output
     * @throws UserException
     * @return array
     */
    public static function askPassword($command, $input, $output)
    {
        $helper = $command->getHelper('question');
        $question = new Question('What is the database password?');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $password = SymfonyAsk::ask($helper, $input, $output, $question);
        $user = UserForm::user()->findUser(Password::getUser(), $password);
        if (! $user) {
            throw new UserException('User not found, please check user exist or password is right!');
        }
        return $user;
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
}