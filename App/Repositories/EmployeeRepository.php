<?php
declare(strict_types = 1);

namespace App\Repositories;

use App\Core\EntityManager;
use App\Models\Employee;
use PDO;

class EmployeeRepository
{
    private EntityManager $entityManager;
    private DepartmentRepository $departmentRepo;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager    = $entityManager;
        $this->departmentRepo   = new DepartmentRepository($entityManager);
    }

    public function findAll(): array
    {
        $stmt = $this->entityManager->getConnection()
            ->query("SELECT * FROM employees");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->hydrateEmployees($rows);
    }

    public function findByDepartment(int $departmentId): array
    {
        $stmt = $this->entityManager->getConnection()
            ->prepare("SELECT * FROM employees WHERE department_id = :deptId");
        $stmt->execute(['deptId' => $departmentId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->hydrateEmployees($rows);
    }

    public function find(int $id): ?Employee
    {
        $stmt = $this->entityManager->getConnection()
            ->prepare("SELECT * FROM employees WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->hydrateEmployee($data);
    }

    public function add(Employee $employee): bool
    {
        $deptId = $employee->getDepartment()?->getId();
        $stmt = $this->entityManager->getConnection()
            ->prepare("INSERT INTO employees (name, salary, position, department_id) VALUES (?, ?, ?, ?)");
        return $stmt->execute([
            $employee->getName(),
            $employee->getSalary(),
            $employee->getPosition(),
            $deptId,
        ]);
    }

    public function update(Employee $employee): bool
    {
        $deptId = $employee->getDepartment()?->getId();
        $stmt = $this->entityManager->getConnection()
            ->prepare("UPDATE employees SET name = ?, salary = ?, position = ?, department_id = ? WHERE id = ?");
        return $stmt->execute([
            $employee->getName(),
            $employee->getSalary(),
            $employee->getPosition(),
            $deptId,
            $employee->getId(),
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->entityManager->getConnection()
            ->prepare("DELETE FROM employees WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * @param array[] $rows
     * @return Employee[]
     */
    private function hydrateEmployees(array $rows): array
    {
        $employees = [];
        foreach ($rows as $data) {
            $employees[] = $this->hydrateEmployee($data);
        }
        return $employees;
    }

    /**
     * @param array $data
     * @return Employee
     */
    private function hydrateEmployee(array $data): Employee
    {
        $employee = new Employee();
        $employee->setId((int)$data['id']);
        $employee->setName((string)$data['name']);
        $employee->setSalary((float)$data['salary']);
        $employee->setPosition((string)$data['position']);

        $department = $this->departmentRepo->find((int)$data['department_id']);
        if ($department !== null) {
            $employee->setDepartment($department);
        }

        return $employee;
    }
}
