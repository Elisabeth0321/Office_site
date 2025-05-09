<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\TemplateEngine;
use App\Services\DepartmentService;

class DepartmentController
{
    private DepartmentService $departmentService;
    private TemplateEngine $templateEngine;

    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
        $this->templateEngine = new TemplateEngine();
    }

    public function listAction(): void
    {
        $departments = $this->departmentService->getAllDepartments();

        echo $this->templateEngine->render(
            __DIR__ . '/../../public/views/department/department_list.html',
            ['departments' => $departments]
        );
    }

    public function deleteAction(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if (!$id) {
            echo "ID не указан";
            return;
        }

        $this->departmentService->deleteDepartment($id);
        header('Location: /departments');
        exit();
    }

    public function addAction(): void
    {
        $name = $_POST['name'] ?? '';
        if ($name === '') {
            echo "Все поля обязательны для заполнения";
            return;
        }

        $this->departmentService->addDepartment($name);
        header('Location: /departments');
        exit();
    }
}
