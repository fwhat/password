<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

namespace Dowte\Password\pass\db\yamlFile;


use Dowte\Password\pass\db\ActiveQuery;
use Dowte\Password\pass\db\DbHelper;
use Dowte\Password\pass\Password;

class YamlQuery extends ActiveQuery
{
    public function one()
    {
        return self::findByWhere($this->where);
    }

    public function all()
    {
        return self::findByWhere($this->where, false);
    }

    protected function findByWhere($where, $one = true)
    {
        $findArr = [];
        $data = Yaml::getData(Yaml::getDbResource(Connection::$config['DB_DIR'], $this->modelClass->name()));
        if (empty($data)) return [];
        $beforeSelectData = $data;
        $itemArr = [];
        foreach ($this->modelClass->attributeLabels() as $k => $v) {
            foreach ($data as &$item) {
                $itemArr[$k][] = isset($item[$k]) ? $item[$k] : '';
                if ($this->select !== self::DEFAULT_SELECT && ! in_array($k, $this->select)) {
                    unset($item[$k]);
                }
            }
        }
        if (empty($itemArr)) {
            return [];
        }
        foreach ($where as $key => $value) {
            if (! isset($itemArr[$key])) {
                DbHelper::$exception->error('The property ' . $key . ' is not exist');
            }
            foreach ($itemArr[$key] as $k => $v) {
                if ($value === $v) {
                    if (Password::compareArray($beforeSelectData[$k], $where)) {
                        if ($one) return $data[$k];
                        $findArr[] = $data[$k];
                    }
                }
            }
        }

        foreach ($findArr as &$v) {
            foreach ($v as &$item) {
                if ($item ===  null) {
                    $item = '';
                }
            }
        }

        return $findArr;
    }
}