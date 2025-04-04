<?php

namespace App\Models;

class Department
{
    public $id;
    public $name;

    public function __construct()
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}