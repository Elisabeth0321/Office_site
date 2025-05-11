<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\TemplateEngine;
use App\Services\DepartmentService;
use Throwable;

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
        $templatePath = __DIR__ . '/../../public/views/department/department_list.html';

        try {
            $departments = $this->departmentService->getAllDepartments();
            echo $this->templateEngine->render(
                $templatePath,
                ['departments' => $departments]
            );
        } catch (Throwable $e) {
            error_log('Ошибка в listAction (DepartmentController): ' . $e->getMessage());
            http_response_code(500);
            echo "Ошибка при загрузке списка отделов.";
        }
    }

    public function deleteAction(): void
    {
        try {
            $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
            if (!$id) {
                http_response_code(400);
                echo "ID отдела не указан";
                return;
            }

            $this->departmentService->deleteDepartment($id);
            header('Location: /departments');
            exit();
        } catch (Throwable $e) {
            error_log('Ошибка в deleteAction (DepartmentController): ' . $e->getMessage());
            http_response_code(500);
            echo "Ошибка при удалении отдела.";
        }
    }

    public function addAction(): void
    {
        try {
            $name = $_POST['name'] ?? '';
            if ($name === '') {
                http_response_code(400);
                echo "Все поля обязательны для заполнения";
                return;
            }

            $this->departmentService->addDepartment($name);
            header('Location: /departments');
            exit();
        } catch (Throwable $e) {
            error_log('Ошибка в addAction (DepartmentController): ' . $e->getMessage());
            http_response_code(500);
            echo "Ошибка при добавлении отдела.";
        }
    }
}
