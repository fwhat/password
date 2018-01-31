<?php

namespace Dowte\Password\pass\db;

use Dowte\Password\pass\Password;

class ActiveRecord implements ActiveRecordInterface
{
    /**
     * @var BaseActiveRecordInterface
     */
    protected static $db;

    /**
     * @var QueryInterface
     */
    protected static $query;

    private $attributeLabels = [];

    protected static $className;

    /**
     * @var ActiveRecordInterface
     */
    protected static $model;

    public function __construct()
    {
        self::setDb($this);
    }

    public function attributeLabels()
    {
    }

    public function name()
    {
    }

    public function rules()
    {
    }

    /**
     * @return QueryInterface
     */
    public static function find()
    {
        if (self::$className === null) {
            self::$className = get_called_class();
        }
        if (self::$db == null) {
            self::setDb();
        }
        return self::$query = (self::$db)::find();
    }

    public function all()
    {
        return (self::$query)->all();
    }

    public function one()
    {
        return (self::$query)->one();
    }

    /**
     * @return integer
     */
    public function save()
    {
         return (self::$db)->save();
    }

    public function __set($name, $value)
    {
        if (isset($this->attributeLabels()[$name])) {

            $this->attributeLabels[$name] = $value;
        } else {
            throw new \Exception('Undefined property: ' . $name);
        }
    }

    public function __get($name)
    {
        if (isset($this->attributeLabels[$name])) {
            return $this->attributeLabels[$name];

        } else {
            throw new \Exception('Undefined property: ' . $name);
        }
    }

    public static function setDb($model = null)
    {
        self::$model = $model ?: new self::$className();
        self::$db = new Password::$dbClass(array_merge(Password::$dbConfig,
            ['name' => self::$model->name(), 'model' => self::$model]));
    }
}