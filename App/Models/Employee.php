<?php

namespace App\Models;

class Employee
{
    public $id;
    public $name;
    public $salary;
    public $position;
    public $department;

    public function __construct() {
    }

    public function getName(): string {
        return $this->name;
    }

    public function getPosition(): string {
        return $this->position;
    }

    public function getSalary(): float {
        return $this->salary;
    }

    public function getDepartment(): string {
        return $this->department;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setPosition(string $position): void
    {
        $this->position = $position;
    }

    public function setSalary(float $salary): void
    {
        $this->salary = $salary;
    }

    public function setDepartment(string $department): void
    {
        $this->department = $department;
    }

}