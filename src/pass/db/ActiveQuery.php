<?php

namespace Dowte\Password\pass\db;

use Dowte\Password\pass\exceptions\QueryException;

abstract class ActiveQuery implements QueryInterface
{
    public $select = [];

    public $where = '';

    /**
     * @param $select
     * @return ActiveQuery
     * @throws QueryException
     */
    public function select($select = '*')
    {
        if(is_array($select)) {
            $this->select = $select;
        } elseif (is_string($select)) {
            $this->select = explode(',', str_replace(['`', ' '], '', $select));
        } else {
            throw new QueryException('Set select error');
        }
        return $this;
    }

    /**
     * @param $where
     * @return ActiveQuery
     * @throws QueryException
     */
    public function where($where)
    {
        if(is_array($where)) {
            foreach ($where as $k => $value) {
                $this->where .= "`$k`='$value' AND";
            }
            $this->where = trim($this->where, 'AND');
        } elseif (is_string($where)) {
            $this->where = $where;
        } else {
            throw new QueryException('Set select error');
        }
        return $this;
    }

//    /**
//     * @return array
//     */
//    public function all()
//    {
//
//    }
}