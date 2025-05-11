<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\TemplateEngine;
use App\Services\DepartmentService;
use App\Services\EmployeeService;
use Throwable;

class EmployeeController
{
    private EmployeeService $employeeService;
    private DepartmentService $departmentService;
    private TemplateEngine $templateEngine;

    public function __construct(EmployeeService $employeeService, DepartmentService $departmentService)
    {
        $this->employeeService = $employeeService;
        $this->departmentService = $departmentService;
        $this->templateEngine = new TemplateEngine();
    }

    public function listByDepartmentAction(): void
    {
        $templatePath = __DIR__ . '/../../public/views/employee/employee_list.html';

        try {
            $deptId = isset($_GET['id']) ? (int)$_GET['id'] : null;
            if (!$deptId) {
                http_response_code(400);
                echo "ID отдела не указан";
                return;
            }

            $department = $this->departmentService->getDepartmentById($deptId);
            if (!$department) {
                http_response_code(400);
                echo "Отдел не найден";
                return;
            }

            $employees = $this->employeeService->getEmployeesByDepartment($deptId);

            echo $this->templateEngine->render(
                $templatePath,
                [
                    'department' => $department,
                    'employees' => $employees,
                ]
            );
        } catch (Throwable $e) {
            error_log('Ошибка в listByDepartmentAction: ' . $e->getMessage());
            http_response_code(500);
            echo "Ошибка при получении списка сотрудников.";
        }
    }

    public function deleteAction(): void
    {
        try {
            $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
            if (!$id) {
                http_response_code(400);
                echo "ID сотрудника не указан";
                return;
            }

            $this->employeeService->deleteEmployee($id);
            header("Location: /account");
            exit();
        } catch (Throwable $e) {
            error_log('Ошибка в deleteAction: ' . $e->getMessage());
            http_response_code(500);
            echo "Ошибка при удалении сотрудника.";
        }
    }

    public function addFormAction(): void
    {
        try {
            $templatePath = __DIR__ . '/../../public/views/employee/employee_add_form.html';
            $departments = $this->departmentService->getAllDepartments();
            echo $this->templateEngine->render($templatePath, ['departments' => $departments]);
        } catch (Throwable $e) {
            error_log('Ошибка в addFormAction: ' . $e->getMessage());
            http_response_code(500);
            echo "Ошибка при загрузке формы.";
        }
    }

    public function addAction(): void
    {
        $name = $_POST['name'] ?? '';
        $salary = isset($_POST['salary']) ? (float)$_POST['salary'] : 0.0;
        $position = $_POST['position'] ?? '';
        $deptId = isset($_POST['departmentId']) ? (int)$_POST['departmentId'] : 0;

        if ($name === '' || $position === '') {
            http_response_code(400);
            echo "Все поля обязательны для заполнения";
            return;
        }

        try {
            $this->employeeService->addEmployee($name, $salary, $position, $deptId);
            header("Location: /employees/department?id=$deptId");
            exit();
        } catch (Throwable $e) {
            error_log('Ошибка в addAction: ' . $e->getMessage());
            http_response_code(500);
            echo "Ошибка при добавлении сотрудника.";
        }
    }

    public function editFormAction(): void
    {
        try {
            $templatePath = __DIR__ . '/../../public/views/employee/employee_edit_form.html';
            $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
            if (!$id) {
                http_response_code(400);
                echo "Не указан ID сотрудника";
                return;
            }

            $employee = $this->employeeService->getEmployeeById($id);
            if (!$employee) {
                http_response_code(400);
                echo "Сотрудник не найден";
                return;
            }

            $departments = $this->departmentService->getAllDepartments();
            echo $this->templateEngine->render($templatePath, ['employee' => $employee, 'departments' => $departments]);
        } catch (Throwable $e) {
            error_log('Ошибка в editFormAction: ' . $e->getMessage());
            http_response_code(500);
            echo "Ошибка при загрузке формы редактирования.";
        }
    }

    public function editAction(): void
    {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
        if (!$id) {
            http_response_code(400);
            echo "Не указан ID сотрудника";
            return;
        }

        $name = $_POST['name'] ?? '';
        $salary = isset($_POST['salary']) ? (float)$_POST['salary'] : 0.0;
        $position = $_POST['position'] ?? '';
        $deptId = isset($_POST['departmentId']) ? (int)$_POST['departmentId'] : 0;

        if ($name === '' || $position === '') {
            http_response_code(400);
            echo "Все поля обязательны для заполнения";
            return;
        }

        try {
            $this->employeeService->updateEmployee($id, $name, $salary, $position, $deptId);
            header("Location: /employees/department?id=$deptId");
            exit();
        } catch (Throwable $e) {
            error_log('Ошибка в editAction: ' . $e->getMessage());
            http_response_code(500);
            echo "Ошибка при обновлении данных сотрудника.";
        }
    }
}
