<?php
namespace Dowte\Password\forms;

use Dowte\Password\models\UserModel;
use Dowte\Password\pass\exceptions\UserException;
use Dowte\Password\pass\Password;

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
            return Password::validPassword($password, $user['password']) ? $user : null;
        }
    }

    public function createUser($userName, $password)
    {
        $model = new UserModel();
        $model->username = sha1($userName);
        $model->password = $password;
        $model->save();
        return $model->username;
    }
}