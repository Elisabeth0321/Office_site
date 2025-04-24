<?php
declare(strict_types = 1);

namespace App\Services;

use App\Models\Employee;
use App\Repositories\EmployeeRepository;
use App\Repositories\DepartmentRepository;

class EmployeeService
{
    private EmployeeRepository $employeeRepository;
    private DepartmentRepository $departmentRepository;

    public function __construct(
        EmployeeRepository $employeeRepository,
        DepartmentRepository $departmentRepository
    ) {
        $this->employeeRepository   = $employeeRepository;
        $this->departmentRepository = $departmentRepository;
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

    public function addEmployee(
        string $name,
        float $salary,
        string $position,
        int $departmentId
    ): bool {
        // Загружаем Department по ID
        $department = $this->departmentRepository->find($departmentId);
        if (!$department) {
            throw new \InvalidArgumentException("Отдел с ID {$departmentId} не найден");
        }

        $employee = new Employee();
        $employee->setId(0);
        $employee->setName($name);
        $employee->setSalary($salary);
        $employee->setPosition($position);
        $employee->setDepartment($department);

        return $this->employeeRepository->add($employee);
    }

    public function updateEmployee(
        int $id,
        string $name,
        float $salary,
        string $position,
        int $departmentId
    ): bool {
        $department = $this->departmentRepository->find($departmentId);
        if (!$department) {
            throw new \InvalidArgumentException("Отдел с ID {$departmentId} не найден");
        }

        $employee = new Employee();
        $employee->setId($id);
        $employee->setName($name);
        $employee->setSalary($salary);
        $employee->setPosition($position);
        $employee->setDepartment($department);

        return $this->employeeRepository->update($employee);
    }
}