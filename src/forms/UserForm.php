<?php
namespace Dowte\Password\forms;

use Dowte\Password\models\UserModel;
use Dowte\Password\pass\PassSecret;

class UserForm
{
    private function __construct()
    {
    }

    public static function user()
    {
        return (new self());
    }

    public function findOne($where, $fields = '*')
    {
        $model = new UserModel();
        return $model::find()->select($fields)->where($where)->one();
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
            return PassSecret::validData($password, $user['password']) ? $user : null;
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