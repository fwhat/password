<?php

namespace Dowte\Password\pass\db;

/**
 * Class BaseConnection
 * @package Dowte\Password\pass\db
 */
abstract class BaseConnection
{
    /**
     * @var array
     */
    public static $config;

    /**
     * @var string
     */
    protected $_activeRecordClass;

    protected $_activeQueryClass;

    /**
     * BaseConnection constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->setActiveRecordClass();
        $this->setActiveQueryClass();
        foreach ($config as $k => $item) {
            if ($k == 'class') continue;
            $this->$k = $item;
        }
        foreach ($this->requireProperties() as $property) {
            if (! isset(self::$config[$property])) {
                DbHelper::$exception->error('The property ' . $property . ' is required!');
            }
        }
        ActiveQuery::$className = $this->_activeQueryClass;
        ActiveRecord::$className = $this->_activeRecordClass;
    }

    public function __set($name, $value)
    {
        if (in_array($name, $this->allowProperties())) {
            self::$config[$name] = $value;
        } else {
            DbHelper::$exception->error('The property ' . $name . ' is invalid! ');
        }
    }

    public function __get($name)
    {
        if(! isset(self::$config[$name])) {
            DbHelper::$exception->error('The property ' . $name . ' is not exists! ');
        }
        return self::$config[$name];
    }

    abstract protected function requireProperties();

    /**
     * @return array
     */
    abstract protected function allowProperties();

    /**
     * @return void
     */
    abstract protected function setActiveRecordClass();

    /**
     * @return mixed
     */
    abstract protected function setActiveQueryClass();
}