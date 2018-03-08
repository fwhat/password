<?php

namespace Dowte\Password\pass\db;

use Dowte\Password\pass\Password;


/**
 * Class BaseConnection
 * @package Dowte\Password\pass\db
 */
abstract class BaseConnection
{
    public $config;

    /**
     * @var string
     */
    protected $_activeRecordClass;

    protected $_activeQueryClass;

    /**
     * BaseConnection constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->setActiveRecordClass();
        $this->setActiveQueryClass();
        foreach ($config as $k => $item) {
            if ($k == 'class') continue;
            if (in_array($k, $this->allowProperty())) {
                $this->config[$k] = $item;
            } else {
                Password::error('The property ' . $k . ' is invalid! ');
            }
        }
        ActiveQuery::$className = $this->_activeQueryClass;
        ActiveRecord::$className = $this->_activeRecordClass;
    }

    public function __get($name)
    {
        if(! isset($this->config[$name])) {
            die('error');
        }
        return $this->config[$name];
    }

    /**
     * @return array
     */
    abstract protected function allowProperty();

    /**
     * @return void
     */
    abstract protected function setActiveRecordClass();

    /**
     * @return mixed
     */
    abstract protected function setActiveQueryClass();
}