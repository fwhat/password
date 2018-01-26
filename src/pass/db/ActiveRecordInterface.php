<?php
namespace Dowte\Password\pass\db;

interface ActiveRecordInterface extends BaseActiveRecordInterface
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
}