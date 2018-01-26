<?php

namespace Dowte\Password\pass\db;


interface BaseActiveRecordInterface
{
    /**
     * @return QueryInterface;
     */
    public static function find();

    public function save();
}