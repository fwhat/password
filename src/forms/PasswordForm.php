<?php

namespace Dowte\Password\forms;


use Dowte\Password\models\PasswordModel;

class PasswordForm
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
        $model = new PasswordModel();
        return $model::find()->all();
    }

    /**
     * @param string $password
     * @param string $name
     * @param string $description
     * @return int
     */
    public function createUser($password, $name = '', $description = '')
    {
        $model = new PasswordModel();
        $model->name = $name;
        $model->password = $password;
        $model->description = $description;

        return $model->save();
    }
}