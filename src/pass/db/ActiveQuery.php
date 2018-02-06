<?php

namespace Dowte\Password\pass\db;

use Dowte\Password\pass\exceptions\BaseException;
use Dowte\Password\pass\Password;

abstract class ActiveQuery implements QueryInterface
{
    const DEFAULT_SELECT = ['*'];

    public $select = [];

    public $where = [];

    /**
     * @param $select
     * @return ActiveQuery
     */
    public function select($select = self::DEFAULT_SELECT)
    {
        if ($select == '*') {
            $select = self::DEFAULT_SELECT;
        }
        if(is_array($select)) {
            $this->select = $select;
        } elseif (is_string($select)) {
            $this->select = explode(',', str_replace(['`', ' '], '', $select));
        } else {
            Password::error('Set select error', BaseException::QUERY_CODE);
        }
        return $this;
    }

    /**
     * @param $where
     * @return ActiveQuery
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
                    Password::error('Set where error', BaseException::QUERY_CODE);
                }
            }
        } else {
            Password::error('Set where error', BaseException::QUERY_CODE);
        }
        return $this;
    }
}