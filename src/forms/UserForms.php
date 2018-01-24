<?php
namespace Dowte\Password\forms;

use Dowte\Password\models\UserFileModel;

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
        $model = new UserFileModel();
        return $model->all();
    }

    public function createUser($userName, $password)
    {
        $model = new UserFileModel();
        $data['username'] = $userName;
        $data['password'] = password_hash($password, PASSWORD_DEFAULT);

        return $model->save($data);
    }
}