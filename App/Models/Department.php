<?php
declare(strict_types = 1);

namespace App\Models;

class Department
{
    private int $id;
    private string $name;
    /** @var Employee[] */
    private array $employees = [];

    public function __construct(int $id = 0, string $name = '')
    {
        $this->id = $id;
        $this->name = $name;
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

    /**
     * @return Employee[]
     */
    public function getEmployees(): array
    {
        return $this->employees;
    }

    public function addEmployee(Employee $employee): void
    {
        $this->employees[] = $employee;
        $employee->setDepartment($this);
    }

    public function removeEmployee(Employee $employee): void
    {
        foreach ($this->employees as $key => $e) {
            if ($e->getId() === $employee->getId()) {
                unset($this->employees[$key]);
                $employee->setDepartment(null);
                break;
            }
        }
        $this->employees = array_values($this->employees);
    }
}
