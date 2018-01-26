<?php
namespace Dowte\Password\forms;

use Dowte\Password\models\UserModel;

class UserForm
{
    private function __construct()
    {
    }

    public static function user()
    {
        return (new self());
    }

    public function findUser($username, $password)
    {
        $user = UserModel::find()
            ->select('id, password')
            ->where(['username' => $username])
            ->one();

        if (empty($user)) {
            return null;

        } else {
            return password_verify($password, $user['password']) ? $user : null;
        }
    }

    public function createUser($userName, $password)
    {
        $model = new UserModel();
        $model->username = $userName;
        $model->password = password_hash($password, PASSWORD_DEFAULT);

        return $model->save();
    }
}