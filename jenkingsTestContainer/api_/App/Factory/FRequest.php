<?php

namespace App\Factory;

/**
 * Factory class to generate different types of request types
 *
 * The supported types are:
 *
 * 'application/json'
 * 'application/x-www-form-urlencoded'
 * 'FormUrlencoded'
 *
 */
abstract class FRequest extends FBase
{
    /**
     * @inheritdoc
     */
    public static function build(string $class = '', array $constructorParams = [])
    {
        $className = self::getClassName();
        $class = 'App\Services\Request\\' . $className;
        return parent::build($class);
    }

    /**
     * Get the class name, based on the format of the current request
     *
     * @throws \Exception
     */
    private static function getClassName(): string
    {
        if (str_starts_with($_SERVER['CONTENT_TYPE'], 'application/json')) {
            return 'Json';
        }
        if (str_starts_with($_SERVER['CONTENT_TYPE'], 'application/x-www-form-urlencoded')) {
            return 'FormUrlencoded';
        }
        if (str_starts_with($_SERVER['CONTENT_TYPE'], 'multipart/form-data')) {
            return 'Formdata';
        }
        return 'Json';
        throw new \Exception("Invalid type given.");
    }
}