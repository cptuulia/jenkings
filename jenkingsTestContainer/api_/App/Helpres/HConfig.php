<?php

namespace App\Helpres;

class HConfig
{

    static private $config = [];

    private static function getConfigFile(): array
    {
        if (empty(self::$config)) {
            self::$config = require __DIR__ . '/../../config/config.php';
        }
        return self::$config;
    }

    /**
     * @return mixed
     */
    public static function getConfig(string $param)
    {
        $path = explode('.', $param);
        $config = self::getConfigFile();
        $first = current($path);
        if (!isset($config[$first])) {
            return null;
        }

        foreach ($path as $item) {
            if (isset($config[$item])) {
                $config = $config[$item];
            } else {
                return null;
            }
        }
        return $config;
    }

    public static function setConfig(string $param, string $value): void
    {
        $path = explode('.', $param);
        $config = self::getConfigFile();


        $tmp = &$config;
        for ($index = 0; $index < count($path); $index++) {
            $item = $path[$index];
            if (isset($config[$item])) {
                $tmp = &$config[$item];
            } else {
                $tmp[$item] = [];
                $tmp = &$tmp[$item];
            }
            if ($index == count($path) - 1) {
                $tmp = $value;
            }
        }
        self::$config = $config;
    }


}