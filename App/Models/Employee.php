<?php

namespace App\Models;

class Employee
{
    private int $id;
    private string $name;
    private float $salary;
    private string $position;
    private ?Department $department = null;

    public function __construct(int $id = 0, string $name = '', float $salary = 0.0, string $position = '')
    {
        $this->id = $id;
        $this->name = $name;
        $this->salary = $salary;
        $this->position = $position;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSalary(): float
    {
        return $this->salary;
    }

    public function setSalary(float $salary): void
    {
        $this->salary = $salary;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function setPosition(string $position): void
    {
        $this->position = $position;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): void
    {
        $this->department = $department;
    }

    /**
     * Удобный метод, если нужен только ID департамента
     */
    public function getDepartmentId(): ?int
    {
        return $this->department?->getId();
    }
}
