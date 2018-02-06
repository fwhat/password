<?php

namespace Dowte\Password\forms;


use Dowte\Password\models\PasswordModel;
use Dowte\Password\pass\PassSecret;
use Dowte\Password\pass\Password;

class PasswordForm
{
    private function __construct()
    {
    }

    public static function pass()
    {
        return (new self());
    }

    public function findPassword($userId, $name)
    {
        $model = new PasswordModel();
        $passwords = $model::find()->select('password, name')->where(['user_id' => $userId])->all();
        foreach ($passwords as $password) {
            if (PassSecret::validData($password['name'], $name)) {
                return $password['password'];
            }
        }
        return false;
    }

    public function findModels($fields, $where = [])
    {
        $model = new PasswordModel();
        return $model::find()->select($fields)->where($where)->all();
    }

    public function findOne($where = [], $fields = '*')
    {
        $model = new PasswordModel();
        return $model::find()->select($fields)->where($where)->one();
    }

    /**
     * @param integer $userId
     * @param string $password
     * @param string $name
     * @param string $description
     * @return int
     */
    public function createPass($userId, $password, $name = '', $description = '')
    {
        $model = new PasswordModel();
        $model->user_id = $userId;
        $model->name = $name;
        $model->password = $password;
        $model->description = $description;

        return $model->save();
    }

    /**
     * @param string $sprintf the sprintf string has a %s to set name
     * @return string | array
     */
    public function getDecryptedName($sprintf = '')
    {
        $user = UserForm::user()->findOne(['username' => Password::getUser()]);
        $names = $this->findModels('name', ['user_id' => $user['id']]);
        $lists = '';
        if ($sprintf) {
            foreach ($names as $name) {
                $lists .= sprintf($sprintf, PassSecret::decryptedData($name['name']));
            }
            return $lists;
        } else {
            return array_map(function($arr) {
                return PassSecret::decryptedData($arr['name']);
            }, $names);
        }
    }
}