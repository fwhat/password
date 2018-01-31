<?php

namespace Dowte\Password\forms;


use Dowte\Password\models\PasswordModel;

class PasswordForm
{
    private function __construct()
    {
    }

    public static function pass()
    {
        return (new self());
    }

    public function findModels($fields, $where = [])
    {
        $model = new PasswordModel();
        return $model::find()->select($fields)->where($where)->all();
    }

    public function findOne($fields, $where = [])
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
}