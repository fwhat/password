<?php

namespace Dowte\Password\pass\components;


class FileUtil
{
    public static function createFile($path, $mode = '')
    {
        foreach ((array) $path as $value) {
            if (file_exists($value)) continue;
            $fp = fopen($value, 'w+');
            fclose($fp);
            if ($mode) chmod($value, $mode);
        }
    }

    /**
     * 获取路径下的文件
     * @param $path
     * @param $extension
     * @return false | array
     */
    public static function getFiles($path, $extension = '')
    {
        $path = rtrim($path, '/') . '/';
        if (! is_dir($path)) {
            return false;
        }
        $fileNames = [];
        $files = scandir($path);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..' || is_dir($path . $file)) continue;
            if ($extension) {
                if (pathinfo($file, PATHINFO_EXTENSION) != $extension) continue;
            }
            $fileNames[] = $file;
        }

        return $fileNames;
    }

    /**
     * 获取相对路径写法的绝对路径写法
     * @param $path
     * @return string
     */
    public static function _realPath($path)
    {
        $arr = explode('/', $path);
        $realPath = [];
        foreach ($arr as $key => $value) {
            if ($value == '..') {
                array_pop($realPath);
                continue;
            }
            if ($value == '.') {
                continue;
            }
            $realPath[] = $value;
        }
        return implode('/', $realPath);
    }
}