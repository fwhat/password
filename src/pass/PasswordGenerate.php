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
    public $length = 12;

    /**
     * @var int max level 4
     */
    public $level = 3;

    public function __construct(array $config = [])
    {
        foreach ($config as $k => $value){
            ! property_exists($this, $k) or $this->$k = $value;
        }
    }

    /**
     * @param $level
     * @return $this
     */
    public function setLevel($level)
    {
        $this->level = $level > 4 || $level <= 0 ? $this->level : $level;
        return $this;
    }

    /**
     * @param $length
     * @return $this
     */
    public function setLength($length)
    {
        $this->length = $length > 100 || $length <= 0 ? $this->length : $length;
        return $this;
    }

    /**
     * @return string
     */
    public function get()
    {
        $rand = new RandomString($this->length);
        foreach ($this->level() as $method) {
            $rand->$method();
        }
        return $rand->generate();
    }

    /**
     * @return \Generator
     */
    private function level()
    {
        $levelMethod = ['setNum', 'setLower', 'setUpper', 'setSpecialChar'];
        for ($i = 0; $i < $this->level; $i ++) {
            yield $levelMethod[$i];
        }
    }

    /**
     * @return array
     */
    public function allLevel()
    {
        return [self::LEVEL_ONE, self::LEVEL_TWO, self::LEVEL_THREE, self::LEVEL_FOUR];
    }
}