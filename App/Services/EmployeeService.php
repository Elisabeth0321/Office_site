<?php

namespace App\Services;

use App\Models\Employee;
use App\Repositories\EmployeeRepository;

class EmployeeService
{
    private EmployeeRepository $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function getAllEmployees(): array
    {
        return $this->employeeRepository->findAll();
    }

    public function getEmployeeById(int $id): ?Employee
    {
        return $this->employeeRepository->find($id);
    }

    public function deleteEmployee(int $id): bool
    {
        return $this->employeeRepository->delete($id);
    }

    public function addEmployee(string $name, float $salary, string $position, string $department): bool
    {
        $employee = new Employee();
        $employee->id = null;
        $employee->name = $name;
        $employee->salary = $salary;
        $employee->position = $position;
        $employee->department = $department;
        return $this->employeeRepository->add($employee);
    }

    public function updateEmployee(int $id, string $name, float $salary, string $position, string $department): bool
    {
        $employee = new Employee();
        $employee->id = $id;
        $employee->name = $name;
        $employee->salary = $salary;
        $employee->position = $position;
        $employee->department = $department;
        return $this->employeeRepository->update($employee);
    }
}