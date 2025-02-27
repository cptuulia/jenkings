<?php

namespace App\Models;


/**
 * Model  to handle Tags
 */
class LogStatements extends BaseModel
{

    public static $INFO = 'INFO';
    public static $ERROR = 'ERROR';
    /**
     * @var string
     */
    protected $table = 'log_statements';

    protected int $id;
    protected string $type;
    protected string $message;
    protected string $file;
    protected  string $createdAt;
    protected string $updatedAt;

}