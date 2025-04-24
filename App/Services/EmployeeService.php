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

    public function getEmployeesByDepartment(int $departmentId): array
    {
        return $this->employeeRepository->findByDepartment($departmentId);
    }

    public function getEmployeeById(int $id): ?Employee
    {
        return $this->employeeRepository->find($id);
    }

    public function deleteEmployee(int $id): bool
    {
        return $this->employeeRepository->delete($id);
    }

    public function addEmployee(string $name, float $salary, string $position, int $departmentId): bool
    {
        $employee = new Employee();
        $employee->setId(0);
        $employee->setName($name);
        $employee->setSalary($salary);
        $employee->setPosition($$position);
        $employee->setDepartmentId($departmentId);
        return $this->employeeRepository->add($employee);
    }

    public function updateEmployee(int $id, string $name, float $salary, string $position, string $departmentId): bool
    {
        $employee = new Employee();
        $employee->setId($id);
        $employee->setName($name);
        $employee->setSalary($salary);
        $employee->setPosition($position);
        $employee->setDepartmentId($departmentId);
        return $this->employeeRepository->update($employee);
    }
}