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
    public static function getData($dbResource)
    {
        return Syaml::parseFile($dbResource);
    }

    public static function getFromFile($tableName)
    {
        return '.' . strtolower($tableName) . '.yaml';
    }

    public static function getDbResource($dbDir, $tableName)
    {
        $resource = rtrim($dbDir, '/') . '/' . self::getFromFile($tableName);
        if (! file_exists($resource)) {
            FileUtil::createFile($resource);
            file_put_contents($resource, '');
            chmod($resource, 0600);
        }
        return $resource;
    }

    public static function dumpInsertNote($data, $dbResource)
    {
        //todo use fopen xxx
        file_put_contents($dbResource, '#' . Syaml::dump($data, 0) . PHP_EOL, FILE_APPEND);
    }

    public function dumpInsertData($data, $dbResource)
    {
        file_put_contents($dbResource, Syaml::dump([$data]), FILE_APPEND);
    }

    public function updateResource($data, $dbResource)
    {
        return file_put_contents($dbResource, empty($data) ? '' : Syaml::dump($data));
    }
}