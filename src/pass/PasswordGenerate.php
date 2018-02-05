<?php

namespace Dowte\Password\pass;


use Dowte\Password\pass\components\RandomString;

class PasswordGenerate
{
    const LEVEL_ONE = 1; //only numbers
    const LEVEL_TWO = 2; //numbers and lower chars
    const LEVEL_THREE = 3; //numbers lower chars and upper chars
    const LEVEL_FOUR = 4; //numbers lower chars upper chars and special chars

    /**
     * @var int max length 100
     */
    protected $_length = 12;

    /**
     * @var int max level 4
     */
    protected $_level = 3;

    protected $_password;

    public static function gen()
    {
        return new self();
    }

    public function setLevel($level)
    {
        $this->_level = $level > 4 || $level <= 0 ? $this->_level : $level;
        return $this;
    }

    public function setLength($length)
    {
        $this->_length = $length > 100 || $length <= 0 ? $this->_length : $length;
        return $this;
    }

    public function get()
    {
        $rand = new RandomString($this->_length);
        foreach ($this->level() as $method) {
            $rand->$method();
        }
        return $rand->generate();
    }

    private function level()
    {
        $levelMethod = ['setNum', 'setLower', 'setUpper', 'setSpecialChar'];
        for ($i = 0; $i < $this->_level; $i ++) {
            yield $levelMethod[$i];
        }
    }

    public static function allLevel()
    {
        return [self::LEVEL_ONE, self::LEVEL_TWO, self::LEVEL_THREE, self::LEVEL_FOUR];
    }

    private function __construct()
    {
    }
}