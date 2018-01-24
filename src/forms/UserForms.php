<?php
namespace Dowte\Password\forms;

use Dowte\Password\models\UserModel;

class UserForms
{
    private function __construct()
    {
    }

    public static function user()
    {
        return (new self());
    }

    public function findModels()
    {
        $model = new UserModel();
        return $model::find()->all();
    }

    public function createUser($userName, $password)
    {
        $model = new UserModel();
        $model->username = $userName;
        $model->password = password_hash($password, PASSWORD_DEFAULT);

        return $model->save();
    }
}