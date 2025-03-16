<?php

namespace App\Repositories;

use App\Core\EntityManager;
use App\Models\Employee;
use PDO;

class EmployeeRepository
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findAll(): array
    {
        $stmt = $this->entityManager->getConnection()->query("SELECT * FROM employees");
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, Employee::class);
        return $result;
    }

    public function find(int $id): ?Employee
    {
        $stmt = $this->entityManager->getConnection()->prepare("SELECT * FROM employees WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $employee = new Employee();
        $employee->id = $data['id'];
        $employee->name = $data['name'];
        $employee->salary = $data['salary'];
        $employee->position = $data['position'];
        $employee->department = $data['department'];

        return $employee;
    }

    public function add(Employee $employee): bool
    {
        $stmt = $this->entityManager->getConnection()->prepare(
            "INSERT INTO employees (name, salary, position, department) VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([
            $employee->getName(),
            $employee->getSalary(),
            $employee->getPosition(),
            $employee->getDepartment()
        ]);
    }

    public function update(Employee $employee): bool
    {
        $stmt = $this->entityManager->getConnection()->prepare(
            "UPDATE employees SET name = ?, salary = ?, position = ?, department = ? WHERE id = ?"
        );
        return $stmt->execute([
            $employee->getName(),
            $employee->getSalary(),
            $employee->getPosition(),
            $employee->getDepartment(),
            $employee->id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->entityManager->getConnection()->prepare("DELETE FROM employees WHERE id = ?");
        return $stmt->execute([$id]);
    }
}