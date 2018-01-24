<?php

namespace Dowte\Password\db;

use Dowte\Password\base\FileItemInterface;

abstract class FileDB implements FileItemInterface
{
    public $separate = ';';

    public $eof = "\n";

    public $data;

    public $db;

    /**
     * FileDB constructor.
     */
    public function __construct()
    {
        $this->db = (FileSystem::fp(['fileName' => $this->fileName()]));
    }

    public function find()
    {
        return $this;
    }

    public function select()
    {
        return $this;
    }

    public function where()
    {
        return $this;
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

    public function save($model)
    {
        $content = '';
        $this->data = $this->line2array($this->db->_fgets());
        foreach ($this->attributeLabels() as $k => $v) {
            if ($k === 'id') continue;
            $content .= $model[$k] . $this->separate;
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

    abstract public function attributeLabels();

    abstract public function rules();

    abstract public function fileName();
}