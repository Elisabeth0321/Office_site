<?php

namespace App\Services;

use App\Models\Department;
use App\Repositories\DepartmentRepository;

class DepartmentService
{
    private DepartmentRepository $departmentRepository;

    public function __construct(DepartmentRepository $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }

    public function getAllDepartments(): array
    {
        return $this->departmentRepository->findAll();
    }

    public function getDepartmentById(int $id): ?Department
    {
        return $this->departmentRepository->find($id);
    }

    public function deleteDepartment(int $id): bool
    {
        return $this->departmentRepository->delete($id);
    }

    public function addDepartment(string $name): bool
    {
        $department = new Department();
        $department->setId(0);
        $department->setName($name);
        return $this->departmentRepository->add($department);
    }

    public function updateDepartment(int $id, string $name): bool
    {
        $department = new Department();
        $department->setId($id);
        $department->setName($name);
        return $this->departmentRepository->update($department);
    }
}