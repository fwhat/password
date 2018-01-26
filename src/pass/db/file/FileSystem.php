<?php

namespace Dowte\Password\pass\db\file;

class FileSystem
{
    public static $baseDir = __DIR__ . '/../../../../data/';
    public static $dataExtension = '.data';

    public static $fpr;

    public static $fpw;

    public static $name;

    public function init()
    {
        self::$fpr = fopen(self::$baseDir . self::$name . self::$dataExtension, 'r');
        self::$fpw = fopen(self::$baseDir . self::$name . self::$dataExtension, 'a');
    }

    public static function _fgets()
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

