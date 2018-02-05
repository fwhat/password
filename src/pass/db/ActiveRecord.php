<?php

namespace Dowte\Password\pass\db;

use Dowte\Password\pass\Password;

class ActiveRecord implements ActiveRecordInterface
{
    /**
     * @var BaseActiveRecordInterface
     */
    protected static $_db;

    /**
     * @var QueryInterface
     */
    protected static $_query;

    protected static $_className;

    /**
     * @var ActiveRecordInterface
     */
    protected static $_model;

    private $_attributeLabels = [];

    public function __construct()
    {
        self::setDb($this);
    }

    public function attributeLabels(){}

    public function name(){}

    public function rules(){}

    /**
     * @return QueryInterface
     */
    public static function find()
    {
        if (self::$_className === null) {
            self::$_className = get_called_class();
        }
        if (self::$_db == null) {
            self::setDb();
        }
        return self::$_query = (self::$_db)::find();
    }

    public function all()
    {
        return (self::$_query)->all();
    }

    public function one()
    {
        return (self::$_query)->one();
    }

    /**
     * @return integer
     */
    public function save()
    {
         return (self::$_db)->save();
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

    private static function setDb($model = null)
    {
        self::$_model = $model ?: new self::$_className();
        self::$_db = new Password::$dbClass(array_merge(Password::$dbConfig,
            ['_name' => self::$_model->name(), '_model' => self::$_model]));
    }
}