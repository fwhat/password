<?php

namespace Dowte\Password\pass\db;


interface QueryInterface
{
    /**
     * @return QueryInterface
     */
    public function select();

    /**
     * @return QueryInterface
     */
    public function where();

    /**
     * @return array
     */
    public function one();

    /**
     * @return array
     */
    public function all();
}