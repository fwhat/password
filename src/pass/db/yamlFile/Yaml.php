<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

namespace Dowte\Password\pass\db\yamlFile;

use Dowte\Password\pass\components\FileUtil;
use Symfony\Component\Yaml\Yaml as Syaml;

class Yaml
{
    public static $resources = [];

    public static function getData($file)
    {
        return Syaml::parseFile($file);
    }

    public static function getFromFile($tableName)
    {
        return '.' . strtolower($tableName) . '.yaml';
    }

    public static function getDbFile($dbDir, $tableName)
    {
        $resource = rtrim($dbDir, '/') . '/' . self::getFromFile($tableName);
        if (! file_exists($resource)) {
            FileUtil::createFile($resource);
            file_put_contents($resource, '');
            chmod($resource, 0600);
        }
        return $resource;
    }

    public static function dumpInsertNote($data, $file)
    {
        file_put_contents($file, '#' . Syaml::dump($data, 0) . PHP_EOL, FILE_APPEND);
    }

    public function dumpInsertData($data, $file)
    {
        $fp = $this->getResource($file);
        fwrite($fp, Syaml::dump([$data]));
//        file_put_contents($file, Syaml::dump([$data]), FILE_APPEND);
    }

    public function updateResource($data, $file)
    {
        return file_put_contents($file, empty($data) ? '' : Syaml::dump($data));
    }

    /**
     * @param $file
     *
     * @return resource $fp
     */
    protected function getResource($file)
    {
        if (!isset(self::$resources[$file])) {
            self::$resources[$file] = fopen($file, 'a+');
        }

        return self::$resources[$file];
    }
}