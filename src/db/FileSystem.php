<?php

namespace Dowte\Password\db;

class FileSystem
{
    const DATA_PATH = __DIR__ . '/../data/';
    const DATA_EXTENSION= '.data';

    public static $fpr;

    public static $fpw;

    public $fileName;

    private function __construct($config = [])
    {
        foreach ($config as $k => $value) {
            $this->$k = $value;
        }
        self::$fpr = fopen(self::DATA_PATH . $this->fileName . self::DATA_EXTENSION, 'r');
        self::$fpw = fopen(self::DATA_PATH . $this->fileName . self::DATA_EXTENSION, 'a');
    }

    public static function fp($config = [])
    {
        return (new self($config));
    }

    public function _fgets()
    {
        return fgets(self::$fpr);
    }

    public function _fwrite($content)
    {
        fwrite(self::$fpw, $content);
    }

    public function _fclose()
    {
        fclose(self::$fpr);
        fclose(self::$fpw);
    }
}

