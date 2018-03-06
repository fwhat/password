<?php

namespace Dowte\Password\pass\db\yamlFile;


use Symfony\Component\Yaml\Yaml as Syaml;

class Yaml
{
    public static function getData($dbResource)
    {
        return Syaml::parseFile($dbResource);
    }

    public static function getFromFile($tableName)
    {
        return $tableName . '.yaml';
    }

    public static function dumpInsertData($data, $dbResource)
    {
        file_put_contents($dbResource, Syaml::dump([$data]), FILE_APPEND);
    }
}