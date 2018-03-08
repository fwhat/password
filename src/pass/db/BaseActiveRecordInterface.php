<?php

namespace Dowte\Password\pass\db;


interface BaseActiveRecordInterface
{
    /**
     * @return integer;
     */
    public function save();
}