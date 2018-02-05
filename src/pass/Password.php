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

    public static function init($config)
    {
        new self($config);
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
            ! $successMessage or $io->error('复制剪贴板失败 !');
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
                        die('Connection db error!');
                    }
                } else {
                    die('Connection db error!');
                }
            }
            if ($name === 'secret') {
                PassSecret::load($config);
            }
        }
    }

    protected static function copy($messages)
    {
        system('echo "'. $messages. '" | pbcopy', $code);
        return $code === 0;
    }
}