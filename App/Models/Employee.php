<?php

namespace App\Models;

class Employee
{
    private int $id;
    private string $name;
    private float $salary;
    private string $position;
    private int $departmentId;
    private Department $department;

    public function __construct()
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function getSalary(): float
    {
        return $this->salary;
    }

    public function getDepartmentId(): int
    {
        return $this->departmentId;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
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

    public function setDepartmentId(int $departmentId): void
    {
        $this->departmentId = $departmentId;
    }

}