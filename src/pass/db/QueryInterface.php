<?php

namespace Dowte\Password\pass\db;


interface QueryInterface
{
    /**
     * @param $select
     * @return QueryInterface
     */
    public function select($select);

    /**
     * @param $where
     * @return QueryInterface
     */
    public function where($where);

    /**
     * @return array
     */
    public function one();

    /**
     * @return array
     */
    public function all();
}