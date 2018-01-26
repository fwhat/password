<?php
namespace Dowte\Password\pass;

use Dowte\Password\pass\db\ActiveRecordInterface;

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

        return file_get_contents(PASS_USER_CONF_DIR . 'user.conf');
    }

    public static function userConf($userName)
    {
        defined(PASS_USER_CONF_DIR) or define(PASS_USER_CONF_DIR, __DIR__ . '/../../data/');

        file_put_contents(PASS_USER_CONF_DIR . 'user.conf', $userName);
    }
}