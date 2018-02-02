<?php

namespace Dowte\Password\pass\db;

use Dowte\Password\pass\exceptions\QueryException;

abstract class ActiveQuery implements QueryInterface
{
    public $select = [];

    public $where = [];

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
            $this->where = $where;
            return $this;
        } elseif (is_string($where)) {
            $items = explode('AND', $where);
            foreach ($items as $item) {
                if (preg_match('/`(.*)`/', $item, $kMatch) && preg_match('/\'(.*)\'/', $item, $vMatch)) {
                    $fileWhere[trim($kMatch[0], '`')] = trim($vMatch[0], '\'"');
                    continue;
                }
                if (($index = mb_strpos($item, '=')) !== false) {
                    $fileWhere[trim(trim(mb_substr($item, 0, $index), '`'))] = trim(trim(mb_substr($item, $index, mb_strlen($item)), '\'"'));

                } else {
                    throw new QueryException('Set where error');
                }
            }
        } else {
            throw new QueryException('Set where error');
        }
        return $this;
    }
}