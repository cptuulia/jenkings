<?php

namespace App\Models;


/**
 * Model  to handle Tags
 */
class AdminUsers extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'admin_users';


    protected int $id;
    protected int $adminRoleId;
    protected string $name;
    protected string $email;
    protected string $emailVerifiedAt;
    protected string $password;
    protected string $rememberToken;
    protected string $apiToken;
    protected int $apiTokenExpires;
    protected string $createdAt;
    protected string $updatedAt;
    protected string $familyName;
    protected string $prefix;
    protected string $street;
    protected string $houseNumber;
    protected string $postalCode;
    protected string $city;
    protected string $tel;
    protected string $company;

    protected string $emailLoginHash;
    protected int $emailLoginHashExpires;
}