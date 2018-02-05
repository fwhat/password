<?php

namespace Dowte\Password\pass\components;


class RandomString
{
    public $charLength;

    public $specialChars = ['!', '@', '#', '$', '%', '^', '&', '*'];

    protected $_string;

    public function __construct($charLength = 12)
    {
        $this->charLength = $charLength;
    }

    public function setNum()
    {
        for ($i = 0; $i < $this->charLength; $i ++){
            $this->_string .= rand(0, 9);
        }

        return $this;
    }

    public function setLower()
    {
        for ($i = 0; $i < $this->charLength; $i ++) {
            $this->_string .= chr(rand(97, 122));
        }

        return $this;
    }

    public function setUpper()
    {
        for ($i = 0; $i < $this->charLength; $i ++) {
            $this->_string .= chr(rand(65, 90));
        }

        return $this;
    }

    public function setSpecialChar()
    {
        for ($i = 0; $i < $this->charLength; $i ++) {
            $this->_string .= $this->specialChars[rand(0, count($this->specialChars) - 1)];
        }

        return $this->_string;
    }

    public function generate()
    {
        $str = '';
        for ($i = 0; $i < $this->charLength; $i ++){
            $str .= $this->_string{rand(0, strlen($this->_string) - 1)};
        }
        return $str;
    }
}