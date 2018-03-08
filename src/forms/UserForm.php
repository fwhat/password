<?php
namespace Dowte\Password\forms;

use Dowte\Password\models\UserModel;
use Dowte\Password\pass\BaseForm;
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

    /**
     * @param $username
     * @param $password
     * @return bool|array *return with a password which is be provide*
     */
    public function findUser($username, $password)
    {
        $user = UserModel::find()
            ->select('id, password')
            ->where(['username' => $username])
            ->one();

        if (empty($user)) {
            return false;

        } else {
            if (password_verify($password, $user['password'])) {
                $user['password'] = $password;
                return $user;
            }
        }
        return false;
    }

    public function createUser($userName, $password)
    {
        $model = new UserModel();
        $model->username = Password::sha256($userName);
        $model->password = password_hash($password, PASSWORD_DEFAULT);
        $model->save();
        return $model->username;
    }
}