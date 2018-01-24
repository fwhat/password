<?php
namespace Dowte\Password\models;

use Dowte\Password\pass\BaseModel;

/**
 * Class UserModel
 * @property string $username
 * @property integer $id
 * @property string $password
 * @package Dowte\Password\db\file\models
 */
class UserModel extends BaseModel
{
    public function name()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            [['username'], 'required'],
            [['id'], 'integer'],
            [['username'], 'string', 'max' => '32'],
            [['password', 'text']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '流水号',
            'username' => '用户名',
            'password' => '密码',
        ];
    }
}