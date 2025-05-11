<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Employee;
use App\Repositories\EmployeeRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\UserRepository;

class EmployeeService
{
    private EmployeeRepository $employeeRepository;
    private DepartmentRepository $departmentRepository;
    private UserRepository $userRepository;

    public function __construct(
        EmployeeRepository   $employeeRepository,
        DepartmentRepository $departmentRepository,
        UserRepository       $userRepository
    )
    {
        $this->employeeRepository = $employeeRepository;
        $this->departmentRepository = $departmentRepository;
        $this->userRepository = $userRepository;
    }

    public function getEmployeesByDepartment(int $departmentId): array
    {
        return $this->employeeRepository->findByDepartment($departmentId);
    }

    public function getEmployeeById(int $id): ?Employee
    {
        return $this->employeeRepository->find($id);
    }

    public function getEmployeeByUserId(int $userId): ?Employee
    {
        return $this->employeeRepository->findByUserId($userId);
    }

    public function addEmployee(
        string $name,
        float  $salary,
        string $position,
        int    $departmentId
    ): bool
    {
        if (!isset($_SESSION['user_id'])) {
            throw new \RuntimeException("Не авторизован");
        }

        $user = $this->userRepository->findById((int)$_SESSION['user_id']);
        if (!$user) {
            throw new \RuntimeException("Пользователь с ID {$_SESSION['user_id']} не найден.");
        }

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
        $employee->setUser($user);

        return $this->employeeRepository->add($employee);
    }

    public function updateEmployee(
        int    $id,
        string $name,
        float  $salary,
        string $position,
        int    $departmentId
    ): bool
    {
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

    public function deleteEmployee(int $id): bool
    {
        return $this->employeeRepository->delete($id);
    }
}