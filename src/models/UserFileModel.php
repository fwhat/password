<?php
namespace Dowte\Password\models;

use Dowte\Password\base\BaseFileModel;

class UserFileModel extends BaseFileModel
{
    public function fileName()
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