<?php

namespace Dowte\Password\pass\db\file;

use Dowte\Password\pass\db\ActiveQuery;
use Dowte\Password\pass\db\ActiveRecordInterface;
use Dowte\Password\pass\db\BaseActiveRecordInterface;
use Dowte\Password\pass\db\QueryInterface;
use Dowte\Password\pass\exceptions\QueryException;

class FileActiveRecord extends File implements BaseActiveRecordInterface
{
    public static $separate = ';';

    public $eof = "\n";

    public static $emptyData = null;

    public static $data;

    /**
     * @var ActiveRecordInterface
     */
    public static $model;

    /**
     * @var ActiveQuery
     */
    public static $query;

    private static $_one = false;

    /**
     * FileActiveRecord constructor.
     * @param $config
     */
    public function __construct($config)
    {
        foreach ($config as $k => $value) {
            ! property_exists(__CLASS__, $k) or static::$$k = $value;
        }
        parent::init();
    }

    /**
     * @return QueryInterface
     */
    public static function find()
    {
        return self::$query = (new FileQuery());
    }

    public function save()
    {
        $content = '';
        self::$data = self::_fgets();
        foreach (self::$model->attributeLabels() as $k => $v) {
            if ($k === 'id') continue;
            $content .= self::$model->$k . self::$separate;
        }
        $id = isset(self::$data['id']) ? ++self::$data['id'] : 1;
        $content = $id . self::$separate . $content . $this->eof;
        $this->_fwrite($content);

        return $id;
    }

    protected static function _fgets()
    {
        $line = parent::_fgets();
        if ($line === false) return false;
        $array =  self::line2array($line);
        $fileWhere = ! empty(self::$query->where) ? self::buildWhere(self::$query->where) : [];
        if ($fileWhere) {
            foreach ($fileWhere as $kWhere => $vWhere) {
                if (isset($array[$kWhere])) {
                    $kWhere != 'password' or die($array[$kWhere] . ' ' . $vWhere);
                    if ($array[$kWhere] != $vWhere) {
                        return self::$emptyData;
                    }
                } else {
                    throw new QueryException('Where param error: ' . $kWhere);
                }
            }
        }
        foreach (self::$model->attributeLabels() as $k => $v){
            if (! empty(self::$query->select)) {
                if (array_search($k, self::$query->select) === false) {
                    unset($array[$k]);
                }
            }
        }
        if (array_diff(array_keys($array), self::$query->select)) {
            throw new QueryException('Select param error: ' . implode(',', array_diff(array_keys($array), self::$query->select)));
        }


        return $array;
    }

    /**
     * @return array
     */
    public static function findOne()
    {
        self::$_one = true;
        return self::findAll();
    }

    /**
     * @return array
     */
    public static function findAll()
    {
        self::$data = [];
        while (($temp = self::_fgets()) !== false) {
            if (empty($temp)) continue;
            $temp === self::$emptyData or self::$data[] = $temp;
            if (self::$_one == true && self::$data) {
                self::$data = self::$data[0];
                break;
            }
        }

        return self::$data;
    }

    private static function buildWhere($where)
    {
        $fileWhere = [];
        $items = explode('AND', $where);
        foreach ($items as $item) {
            if (preg_match('/`(.*)`/', $item, $kMatch) && preg_match('/\'(.*)\'/', $item, $vMatch)) {
                $fileWhere[trim($kMatch[0], '`')] = trim($vMatch[0], '\'"');
                continue;
            }
            if (($index = mb_strpos($item, '=')) !== false) {
                $fileWhere[trim(trim(mb_substr($item, 0, $index), '`'))] = trim(trim(mb_substr($item, $index, mb_strlen($item)), '\'"'));

            } else {
                break;
            }
//            $temp = explode('=', $item);
//            if (! isset($temp[0]) || ! isset($temp[1])) {
//                throw new QueryException('Where param error: ');
//            }
//            $fileWhere[trim($temp[0], '`')] = trim($temp[1], '\'"');
        }
        return $fileWhere;
    }

    private static function line2array($temp)
    {
        $data = [];
        if (empty($temp)) return $data;
        $arr = explode(self::$separate, $temp);
        $index = 0;
        foreach (self::$model->attributeLabels() as $k => $v) {
            $data[$k] = $arr[$index++];
        }
        return $data;
    }
}