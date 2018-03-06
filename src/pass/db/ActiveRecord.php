<?php

namespace Dowte\Password\pass\db;

use Dowte\Password\pass\Password;

class ActiveRecord implements ActiveRecordInterface
{
    /**
     * @var BaseActiveRecordInterface
     */
    protected static $_db;

    protected static $_className;

    /**
     * @var ActiveRecordInterface
     */
    protected static $_model;

    private $_attributeLabels = [];

    public static $modelName;

    public function __construct()
    {
        self::setModelClass($this);
    }

    /**
     * @return QueryInterface
     */
    public static function find()
    {
        self::setModelClass(Password::newObject(get_called_class()));
        self::$_db = Password::newObject(Password::$dbClass, array_merge(Password::$dbConfig,
            ['modelClass' => self::getModel()]));
        return self::getDb()->find();
    }

    /**
     * @return integer
     */
    public function save()
    {
         return $this->getDb()->save();
    }

    public function __set($name, $value)
    {
        if (isset($this->attributeLabels()[$name])) {

            $this->_attributeLabels[$name] = $value;
        } else {
            throw new \Exception('Undefined property: ' . $name);
        }
    }

    public function __get($name)
    {
        if (isset($this->_attributeLabels[$name]) || $this->_attributeLabels[$name] === null) {
            return $this->_attributeLabels[$name];

        } else {
            throw new \Exception('Undefined property: ' . $name);
        }
    }

    protected static function getDb()
    {
        if (self::$_db === null) {
            self::$_db = Password::newObject(Password::$dbClass, array_merge(Password::$dbConfig,
                ['modelClass' => self::getModel()]));
        }
        return self::$_db;
    }

    public static function setModelClass($model)
    {
        if (self::getModel() !== null) {
            if (is_string($model)) {
                if ($model == get_class(self::getModel())) {
                    return;
                }
            }
        }
        self::$_db = null;
        self::$_model = is_string($model) ? Password::newObject($model) : $model;
    }

    protected static function getModel()
    {
        return self::$_model;
    }

    public function attributeLabels(){}

    public function name(){}

    public function rules(){}
}
