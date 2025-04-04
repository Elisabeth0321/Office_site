<?php

namespace App\Controllers;

use App\Core\TemplateEngine;
use App\Services\DepartmentService;

class DepartmentController
{
    private DepartmentService $departmentService;

    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }

    public function listAction(): void
    {
        $departments = $this->departmentService->getAllDepartments();

        $templatePath = __DIR__ . '/../../public/views/department/department_list.html';
        $templateEngine = new TemplateEngine();
        echo $templateEngine->render($templatePath, [
            'departments' => $departments
        ]);
    }

    public function deleteAction(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID не указан";
            return;
        }

        $success = $this->departmentService->deleteDepartment($id);

        if ($success) {
            header('Location: /departments');
        } else {
            echo "Ошибка при удалении отдела";
        }
    }

    public function addAction(): void
    {
        $name = $_POST['name'] ?? '';

        if (empty($name)) {
            echo "Все поля обязательны для заполнения";
            return;
        }

        $success = $this->departmentService->addDepartment($name);

        if ($success) {
            header('Location: /departments');
        } else {
            echo "Ошибка при добавлении отдела";
        }
    }
}