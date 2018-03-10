<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

namespace Dowte\Password\pass;

use Dowte\Password\pass\components\FileUtil;
use Dowte\Password\pass\components\OpensslEncryptHelper;
use Dowte\Password\pass\components\PdHelper;
use Dowte\Password\pass\exceptions\BaseException;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class Password
{
    const BASE_NAMESPACE = 'Dowte\Password\\';

    public static $params = [];

    /**
     * @var PdHelper
     */
    public static $pd;

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
        self::$pd = $this->getPd();
    }

    /**
     * @param $config
     */
    public static function init($config)
    {
        new self($config);
    }

    public function defaultComponents()
    {
        return [
            'generate' => [
                'class' => 'pass\PasswordGenerate',
                'level' => 3,
                'length' => 12
            ]
        ];
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
        if (file_exists($filename)) {
            unlink($filename);
        }
        FileUtil::createFile($filename);
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
     * @param $masterPassword
     * @param $messages
     * @param $io SymfonyStyle
     */
    public static function toPasteDecode($masterPassword, $messages, SymfonyStyle $io)
    {
        $messages = self::decryptedPassword($masterPassword, $messages);
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
            $this->getPd()->params[$name] = $item;
        }
    }

    /**
     * @param array $configs
     */
    protected function loadComponents(array $configs)
    {
        $configs = array_merge($this->defaultComponents(), $configs);
        foreach ((array) $configs as $name => $config) {
            if (isset($config['class']) && class_exists(self::BASE_NAMESPACE . $config['class'])) {
                $this->getPd()->$name = self::newObject(self::BASE_NAMESPACE . $config['class'], $config);
            } else {
                self::error($config['class'] . ' Class not found! ');
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
        if (PASS_ENV == 'dev') {
            $message .= sprintf("[%s#%d]", __FILE__, __LINE__);
        }
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
     * @param $data
     * @return string
     */
    public static function sha256($data)
    {
        return hash('sha256', $data);
    }

    /**
     * @param $masterPassword
     * @param $password
     * @return string
     */
    public static function encryptPassword($masterPassword, $password)
    {
        return OpensslEncryptHelper::cipher()->setPassword($masterPassword)->encryptWithOpenssl($password);
    }

    /**
     * @param $masterPassword
     * @param $password
     * @return string
     */
    public static function decryptedPassword($masterPassword, $password)
    {
        return OpensslEncryptHelper::cipher()->setPassword($masterPassword)->decryptWithOpenssl($password);
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

    /**
     * @param $class
     * @param mixed $param
     * @return mixed
     */
    public static function newObject($class, $param = [])
    {
        return $param ? new $class($param) : new $class();
    }

    /**
     * @param $search
     * @param $replace
     * @param string $toFile
     */
    public static function rewriteConfig($search, $replace, $toFile = '')
    {
        $content = str_replace($search, $replace, file_get_contents(file_exists(CONF_FILE) ? CONF_FILE : CONF_FILE_TEMP));
        file_put_contents($toFile ?: CONF_FILE, $content);
    }

    /**
     * @param $i
     * @param $total
     */
    public static function processOutput($i, $total)
    {
        printf("progress: [%-50s] %s Done\r", str_repeat('#',$i/$total*50), $i . '/' . $total);
    }

    /**
     * @param array $data
     * @param array $handler
     * @return bool
     */
    public static function compareArray(array $data, array $handler)
    {
        foreach ($handler as $k => $value) {
            if ($data[$k] !== $value) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return PdHelper
     */
    protected function getPd()
    {
        if (self::$pd === null) {
            self::$pd = new PdHelper();
        }

        return self::$pd;
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