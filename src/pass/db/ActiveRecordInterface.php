<?php
namespace Dowte\Password\pass\db;

interface ActiveRecordInterface
{
    /**
     * @return array
     */
    public function attributeLabels();

    /**
     * @return string
     */
    public function name();

    /**
     * @return array
     */
    public function rules();

    /**
     * @return ActiveQuery
     */
    public static function find();

    public function save();
}