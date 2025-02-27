<?php

namespace App\Models;


class PdfFiles extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'pdf_files';


    protected int $id;
    protected int $menu;
    protected int $parent;
    protected int $ordering;
    protected string $name;
    protected string $url;
    protected string $text;


    protected string $createdAt;
    protected string $updatedAt;

}