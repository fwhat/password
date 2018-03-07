<?php
namespace Dowte\Password\pass;

use Dowte\Password\pass\db\ActiveRecordInterface;
use Dowte\Password\pass\db\ConnectionInterface;
use Dowte\Password\pass\exceptions\BaseException;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class Password
{
    const BASE_NAMESPACE = 'Dowte\Password\\';

    public static $params = [];

    /**
     * @var ActiveRecordInterface
     */
    public static $dbClass;

    public static $dbConfig;

    /**
     * @var SymfonyStyle
     */
    private static $_io;

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

    /**
     * @param $config
     */
    public static function init($config)
    {
        new self($config);
    }


    /**
     * @return string
     */
    public static function getUserConfFile()
    {
        return PASS_USER_CONF_DIR . '.user';
    }

    /**
     * @return string
     */
    public static function getUser()
    {
        $user = @file_get_contents(self::getUserConfFile());
        if (! $user) {
            self::error('Please create user at first! ');
        }
        return $user;
    }

    /**
     * @param $userName
     * @return bool
     */
    public static function userConfigure($userName)
    {
        $filename = self::getUserConfFile();
        file_put_contents($filename, $userName);
        return chmod($filename, 0400);
    }

    /**
     * @return array
     */
    public static function ways()
    {
        return PasswordDb::ways();
    }

    /**
     * @param $property
     * @return string
     */
    public static function underline2hump($property)
    {
        return lcfirst(str_replace(' ', '',ucwords(str_replace(['_', '-'], ' ', $property))));
    }

    /**
     * @param $messages
     * @param $io SymfonyStyle
     */
    public static function toPasteDecode($messages, SymfonyStyle $io)
    {
        $messages = PassSecret::decryptedData($messages);
        $status = self::copy($messages);
        if ($status) {
            $io->success('复制剪贴板成功 !');
        } else {
            $io->error('复制剪贴板失败 !');
        }
    }

    /**
     * @param $messages
     * @param SymfonyStyle $io
     * @param string $successMessage
     * @param string $errorMessage
     */
    public static function toPaste($messages, SymfonyStyle $io, $successMessage = '复制剪贴板成功 !', $errorMessage = '复制剪贴板失败 !')
    {
        $status = self::copy($messages);
        if ($status) {
            ! $successMessage or $io->success($successMessage);
        } else {
            ! $successMessage or $io->error($errorMessage);
        }
    }

    /**
     * @param array $value
     */
    protected function loadParams(array $value)
    {
        foreach ((array) $value as $name => $item) {
            ! $item or self::$params[$name] = $item;
        }
    }

    /**
     * @param array $configs
     */
    protected function loadComponents(array $configs)
    {
        foreach ((array) $configs as $name => $config) {
            if ($name === 'db') {
                if (isset($config['class']) && class_exists(self::BASE_NAMESPACE . $config['class'])) {
                    $className = self::BASE_NAMESPACE . $config['class'];
                    $instance = new $className();
                    if ($instance instanceof ConnectionInterface) {
                        $instance->init($config);
                    } else {
                        self::error('Connection db error!');
                    }
                } else {
                    self::error('Connection db error!');
                }
            }
            if ($name === 'secret') {
                PassSecret::load($config);
            }
        }
    }

    /**
     * copy data to clipboard
     * @param $messages
     * @return bool
     */
    protected static function copy($messages)
    {
        //todo more os
        system("printf '%s' {$messages} | pbcopy", $code);
        return $code === 0;
    }

    /**
     * error output
     * @param $message
     * @param int $code exit code
     */
    public static function error($message, $code = BaseException::USER_CODE)
    {
        self::getIo()->error($message);
        exit($code);
    }

    /**
     * notice output
     * @param $message
     */
    public static function notice($message)
    {
        self::getIo()->note($message);
    }

    /**
     * success output
     * @param $message
     */
    public static function success($message)
    {
        self::getIo()->success($message);
        exit(0);
    }

    /**
     * encryptUserName
     * @param $username
     * @return string
     */
    public static function encryptUserName($username)
    {
        return hash('sha256', $username);
    }

    /**
     * encryptPasswordKey
     * @param $key
     * @return string
     */
    public static function encryptPasswordKey($key)
    {
        return base64_encode($key);
    }

    /**
     * decryptedPasswordKey
     * @param $key
     * @return string
     */
    public static function decryptedPasswordKey($key)
    {
        return base64_decode($key);
    }

    /**
     * 获取一个命令的位置
     * @param $command
     * @return int|string
     */
    public static function getCommandPath($command)
    {
        if (str_replace(' ', '', $command) !== $command) return 0;
        return exec("which $command");
    }

    public static function newObject($class, $param = [])
    {
        return $param ? new $class($param) : new $class();
    }

    public static function rewriteConfig($search, $replace, $toFile = '')
    {
        $content = str_replace($search, $replace, file_get_contents(file_exists(CONF_FILE) ? CONF_FILE : CONF_FILE_TEMP));
        file_put_contents($toFile ?: CONF_FILE, $content);
    }

    /**
     * @return SymfonyStyle
     */
    private static function getIo()
    {
        if (self::$_io === null) {
            self::$_io = new SymfonyStyle(new ArgvInput(), new ConsoleOutput());
        }
        return self::$_io;
    }
}