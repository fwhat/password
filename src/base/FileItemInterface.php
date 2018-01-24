<?php
namespace Dowte\Password\base;

interface FileItemInterface
{
    public function attributeLabels();

    public function fileName();

    public function rules();
}