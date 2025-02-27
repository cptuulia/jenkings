<?php

namespace App\Services;

use App\Factory\FModel;
use App\Models\BaseModel;
use App\Plugins\Db\Db;
use App\Lib\DbConnection;

abstract class BaseService
{
    /** @var Db  */
    protected $db;

    /**
     * @var string
     */
    protected $modelName;

    /**
     * @var BaseModel
     */
    protected $model;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->db = DbConnection::getConnection();
        $this->model = FModel::build($this->modelName);
    }
}