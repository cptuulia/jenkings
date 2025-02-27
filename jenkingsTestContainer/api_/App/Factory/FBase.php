<?php
/**
 * A generic factory class,
 */

namespace App\Factory;

abstract class FBase
{

    /**
     * @param string $class full name class (including namespace)
     * @param array $constructorParams
     * @return mixed
     * @throws \Exception
     */
    public static function build(string $class = '', array $constructorParams = [])
    {
        // This is needed so that  gatest.tantonius.com works
        // Something in the autoloader is broken....
        $r = \App\Services\Request\FormData::class;
        class_exists($r);

        if (class_exists($class) || true) {
            if (!empty($constructorParams)) {
                $newClass = new \ReflectionClass($class);
                return $newClass->newInstanceArgs($constructorParams);;
            }
            return new $class();;
        } else {
            throw new \Exception("Invalid type given: " . $class);
        }
    }
}
