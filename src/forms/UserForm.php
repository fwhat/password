<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */
namespace Dowte\Password\forms;

use Dowte\Password\models\UserModel;
use Dowte\Password\pass\BaseForm;
use Dowte\Password\pass\Password;

class UserForm extends BaseForm
{
    /**
     * @return UserForm
     */
    public static function user()
    {
        return (new self());
    }

    /**
     * @param array $where ['key' => 'value']
     * @param string $fields
     * @return array
     */
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

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $model = new UserModel();
        return $model->delete(['id' => $id]);
    }

    /**
     * @param $id
     * @param $password string after sha256
     * @param $username string
     * @return integer
     */
    public function update($id, $password = '', $username = '')
    {
        $model = new UserModel();
        $model->id = $id;
        !$username or $model->username = Password::sha256($username);
        !$password or $model->password = password_hash($password, PASSWORD_DEFAULT);

        return $model->save();
    }

    /**
     * @param $userName
     * @param $password
     * @return string
     */
    public function createUser($userName, $password)
    {
        $model = new UserModel();
        $model->username = Password::sha256($userName);
        $model->password = password_hash($password, PASSWORD_DEFAULT);
        $model->save();

        return $model->username;
    }

    private function __construct()
    {
    }
}