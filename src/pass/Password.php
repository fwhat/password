<?php
namespace Dowte\Password\pass;

use Dowte\Password\pass\db\ActiveRecordInterface;
use Dowte\Password\pass\db\ConnectionInterface;
use Dowte\Password\pass\db\DbClear;
use Dowte\Password\pass\db\DbInit;
use Dowte\Password\pass\exceptions\BaseException;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
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

    public static function init($config)
    {
        new self($config);
    }


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

    public static function userConfigure($userName)
    {
        $filename = self::getUserConfFile();
        file_put_contents($filename, $userName);
        return chmod($filename, 0400);
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

    public static function toPaste($messages, SymfonyStyle $io, $successMessage = '复制剪贴板成功 !', $errorMessage = '复制剪贴板失败 !')
    {
        $status = self::copy($messages);
        if ($status) {
            ! $successMessage or $io->success($successMessage);
        } else {
            ! $successMessage or $io->error($errorMessage);
        }
    }

    protected function loadParams(array $value)
    {
        foreach ((array) $value as $name => $item) {
            ! $item or self::$params[$name] = $item;
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

    protected static function copy($messages)
    {
        system("printf '%s' {$messages} | pbcopy", $code);
        return $code === 0;
    }

    /**
     * @param $message
     * @param int $code exit code
     */
    public static function error($message, $code = BaseException::USER_CODE)
    {
        self::getIo()->error($message);
        exit($code);
    }

    public static function notice($message)
    {
        self::getIo()->note($message);
    }

    /**
     * @param $message
     */
    public static function success($message)
    {
        self::getIo()->success($message);
        exit(0);
    }

    public static function _realPath($path)
    {
        $arr = explode('/', $path);
        $realPath = [];
        foreach ($arr as $key => $value) {
            if ($value == '..') {
                array_pop($realPath);
                continue;
            }
            if ($value == '.') {
                continue;
            }
            $realPath[] = $value;
        }
        return implode('/', $realPath);
    }

    private static function getIo()
    {
        if (self::$_io === null) {
            self::$_io = new SymfonyStyle(new ArgvInput(), new ConsoleOutput());
        }
        return self::$_io;
    }
}