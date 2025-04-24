<?php

namespace App\Repositories;

use App\Core\EntityManager;
use App\Models\Department;
use PDO;

class DepartmentRepository
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findAll(): array
    {
        $stmt = $this->entityManager->getConnection()->query("SELECT * FROM departments");
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, Department::class);
        return $result;
    }

    public function find(int $id): ?Department
    {
        $stmt = $this->entityManager->getConnection()->prepare("SELECT * FROM departments WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $department = new Department();
        $department->setId($data['id']);
        $department->setName($data['name']);

        return $department;
    }

    public function add(Department $department): bool
    {
        $stmt = $this->entityManager->getConnection()->prepare(
            "INSERT INTO departments (name) VALUES (?)"
        );
        return $stmt->execute([
            $department->getName(),
        ]);
    }

    public function update(Department $department): bool
    {
        $stmt = $this->entityManager->getConnection()->prepare(
            "UPDATE departments SET name = ? WHERE id = ?"
        );
        return $stmt->execute([
            $department->getName(),
            $department->getId()
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->entityManager->getConnection()->prepare("DELETE FROM departments WHERE id = ?");
        return $stmt->execute([$id]);
    }
}