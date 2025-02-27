<?php

namespace App\Traits;

/**
 * This trait has some string conversion functions
 */
trait TString
{
    function snakeToCamel($input, bool $startWithCapital = false)
    {
        $input = strtolower($input);
        $input = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $input))));
        if ($startWithCapital) {
            $input = ucfirst($input);
        }
        return $input;
    }

    private function fullName(array $params): string
    {
        $fullName = $params['name'];
        if (isset($params['prefix'])) {
            $fullName .= ' ' . $params['prefix'];
        }
        if (isset($params['family_name'])) {
            $fullName .= ' ' . $params['family_name'];
        }
        return $fullName;
    }

    public function stringToArray(string $string): array {
        return  explode(
            ' ',
            strtolower(
                str_replace([',', '.'], '', $string)
            )
        );
    }


    public function htmlLink(string $href, string $name = null): string
    {
        $name = ($name != null) ? $name : $href;
        return '<a href="' . $href . '">' . $name . '</a>';
    }
}