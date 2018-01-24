<?php

namespace Dowte\Password\db\file;

use Dowte\Password\pass\db\ActiveRecordInterface;
use Dowte\Password\pass\db\QueryInterface;

class FileActiveRecord implements ActiveRecordInterface
{
    public $separate = ';';

    public $eof = "\n";

    public $data;

    public $db;

    /**
     * FileActiveRecord constructor.
     */
    public function __construct()
    {
        $this->db = (FileSystem::fp(['fileName' => $this->name()]));
    }

    /**
     * @return QueryInterface
     */
    public static function find()
    {
        return (new FileQuery());
    }

    public function attributeLabels()
    {
        // TODO: Implement attributeLabels() method.
    }

    /**
     * @return string
     */
    public function name()
    {
        // TODO: Implement name() method.
    }

    public function rules()
    {
        // TODO: Implement rules() method.
    }

    public function one()
    {
        return $this->all(true);
    }

    public function all($one = false)
    {
        $this->data;
        while ($temp = $this->db->_fgets() !== false) {
            $this->data = $this->line2array($this->db->_fgets());
            if ($one == true) break;
        }

        return $this->data;
    }

    public function save()
    {
        $content = '';
        $this->data = $this->line2array($this->db->_fgets());
        foreach ($this->attributeLabels() as $k => $v) {
            if ($k === 'id') continue;
            $content .= $this->$k . $this->separate;
        }
        $id = isset($this->data['id']) ? ++$this->data['id'] : 1;
        $content = $id . $this->separate . $content . $this->eof;
        $this->db->_fwrite($content);

        return $id;
    }

    private function line2array($temp)
    {
        $data = [];
        if (empty($temp)) return $data;
        $arr = explode($this->separate, $temp);
        $index = 0;
        foreach ($this->attributeLabels() as $k => $v) {
            $data[$k] = $arr[$index++];
        }
        return $data;
    }
}