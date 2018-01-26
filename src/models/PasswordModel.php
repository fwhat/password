<?php

namespace Dowte\Password\models;


use Dowte\Password\pass\BaseModel;
use Dowte\Password\pass\db\ActiveRecord;


/**
 * Class PasswordModel
 *
 * @property $id
 * @property $password
 * @property $user_id
 * @property $name
 * @property $description
 * @package Dowte\Password\models
 */
class PasswordModel extends BaseModel
{
    public function name()
    {
        return 'password';
    }

    public function rules()
    {
        return [
            [['password'], 'required'],
            [['id', 'user_id'], 'integer'],
            [['name'], 'string', 'max' => '32'],
            [['description'], 'string', 'max' => '64'],
            [['password', 'text']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '流水号',
            'user_id' => '用户id',
            'name' => '名称',
            'description' => '描述',
            'password' => '密码',
        ];
    }
}