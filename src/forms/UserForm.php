<?php
namespace Dowte\Password\forms;

use Dowte\Password\models\UserModel;
use Dowte\Password\pass\BaseForm;
use Dowte\Password\pass\PassSecret;
use Dowte\Password\pass\Password;

class UserForm extends BaseForm
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
            if (PassSecret::validData($password, $user['password'])) {
                $user['password'] = $password;
                return $user;
            }
        }

        return null;
    }

    public function createUser($userName, $password)
    {
        $model = new UserModel();
        $model->username = Password::encryptUserName($userName);
        $model->password = $password;
        $model->save();
        return $model->username;
    }
}