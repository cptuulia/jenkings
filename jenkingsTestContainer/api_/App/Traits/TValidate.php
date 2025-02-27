<?php

namespace App\Traits;

use App\Factory\FModel;
use App\Models\BaseModel;
use App\Plugins\Db\Db;

/**
 * This trait has some string conversion functions
 */

/**
 * @property Db $db
 */
trait TValidate
{
    public static $VALIDATE_STRING = 'string'; //FILTER_SANITIZE_STRING;
    public static $VALIDATE_INTEGER = FILTER_VALIDATE_INT;
    public static $VALIDATE_BOOLEAN = FILTER_VALIDATE_BOOLEAN;
    public static $VALIDATE_EMAIL = FILTER_VALIDATE_EMAIL;
    public static $VALIDATE_PASSWORD = 'FILTER_VALIDATE_PASSWORD';
    public static $VALIDATE_ARRAY = 'array';

    protected function validate(): array
    {
        $request = $this->request;

        $rules = $this->getValidationRules();
        $errors = [];
        foreach ($rules as $column => $rule) {

            $value = $this->request[$column] ?? null;
            $label = "'" . $rule['label'] . "'";
            if ($rule['required']) {
                if (!isset($value)) {
                    $errors[] = $label . ' is verplicht.';
                    continue;
                }
                if (!$value) {
                    $errors[] = $label . ' is verplicht.';
                    continue;
                }
            }
            if (isset($rule['type'])) {
                if (!$this->sanitate($value, $rule['type'])) {
                    $errors[] = $label . ' heeft het verkeerde type';
                    continue;
                }
            }

            if (isset($rule['exists'])) {
                if (!$this->validateExists($rule, $column)) {
                    $errors[] = $label . ' niet gevonden';
                    continue;
                }
            }

            if (isset($rule['unique'])) {
                if (!$this->validateUnique($rule, $column, $request)) {
                    $errors[] = $label . ' bestaat al';
                    continue;
                }
            }
            if (isset($rule['in'])) {
                if (!in_array($value, $rule['in'])) {
                    $errors[] = $label .
                        ' moet een van de volgende waarden hebben : ' .
                        implode(',', $rule['in']);
                    continue;
                }
            }

        }
        return $errors;
    }

    private function validateExists(array $rule, string $column): bool
    {

        /** @var BaseModel $model */
        $model = FModel::build($rule['exists']['model']);
        $row = $model->get($this->request[$column], ['column' => $rule['exists']['column']]);
        return !empty($row);
    }

    private function validateUnique(array $rule, string $column, array $user): bool
    {
        /** @var BaseModel $model */
        $model = FModel::build($rule['unique']['model']);

        $options = [
            'column' => $rule['unique']['column']
        ];
        if (isset($user['id'])) {
            $options['filter'] = [
                'needle' => $user['id'],
                'columns' => 'id',
                'operators' => ['id' => '!=']
            ];
        }
        $row = $model->get(
            $this->request[$column],
            $options
        );
        return empty($row);
    }

    private function sanitate($value, string $type): bool
    {
        if (!$value) {
            return true;
        }
        switch ($type) {
            case self::$VALIDATE_STRING :
                return is_string($value);
                break;
            case self::$VALIDATE_ARRAY :
                return is_array($value);
                break;
            case self::$VALIDATE_PASSWORD :
                return $this->validatePassword($value);
            default:
                return filter_var($value, $type);
        }
    }

    private function validatePassword(string $password): bool
    {


        if (strlen($password) < 8) {
            return false;
        }

        if (!preg_match("#[0-9]+#", $password)) {
            return false;
        }

        if (!preg_match("#[a-zA-Z]+#", $password)) {
            return false;
        }

        return true;
    }

}