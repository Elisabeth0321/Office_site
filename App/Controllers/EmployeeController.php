<?php

namespace App\Controllers;

use App\Services\EmployeeService;
use App\Services\TemplateEngine;

class EmployeeController extends BaseController
{
    private EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function listAction(): void
    {
        $employees = $this->employeeService->getAllEmployees();

        $templatePath = __DIR__ . '/../Views/employee_list.php';
        $templateEngine = new TemplateEngine();
        echo $templateEngine->render($templatePath, [
            'employees' => $employees
        ]);
    }

    public function viewAction(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID не указан";
            return;
        }

        $employee = $this->employeeService->getEmployeeById($id);

        if (!$employee) {
            echo "Сотрудник не найден";
            return;
        }

        $templatePath = __DIR__ . '/../Views/employee_view.php';
        $templateEngine = new TemplateEngine();
        echo $templateEngine->render($templatePath, ['employee' => $employee]);
    }

    public function deleteAction(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID не указан";
            return;
        }

        $success = $this->employeeService->deleteEmployee($id);

        if ($success) {
            header('Location: /employees');
        } else {
            echo "Ошибка при удалении сотрудника";
        }
    }

    public function addFormAction(): void
    {
        $templatePath = __DIR__ . '/../Views/employee_add_form.php';
        $templateEngine = new TemplateEngine();
        echo $templateEngine->render($templatePath);
    }

    public function addAction(): void
    {
        $name = $_POST['name'] ?? '';
        $salary = $_POST['salary'] ?? 0;
        $position = $_POST['position'] ?? '';
        $department = $_POST['department'] ?? '';

        if (empty($name) || empty($position) || empty($department)) {
            echo "Все поля обязательны для заполнения";
            return;
        }

        $success = $this->employeeService->addEmployee($name, $salary, $position, $department);

        if ($success) {
            header('Location: /employees');
        } else {
            echo "Ошибка при добавлении сотрудника";
        }
    }

    public function editFormAction(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID не указан";
            return;
        }

        $employee = $this->employeeService->getEmployeeById($id);

        if (!$employee) {
            echo "Сотрудник не найден";
            return;
        }

        $templatePath = __DIR__ . '/../Views/employee_edit_form.php';
        $templateEngine = new TemplateEngine();
        echo $templateEngine->render($templatePath, ['employee' => $employee]);
    }

    public function editAction(): void
    {
        $id = $_POST['id'] ?? null;
        if (!$id) {
            echo "ID не указан";
            return;
        }

        $existingEmployee = $this->employeeService->getEmployeeById($id);
        if (!$existingEmployee) {
            echo "Сотрудник не найден";
            return;
        }

        $name = $_POST['name'] ?? $existingEmployee->getName();
        $salary = $_POST['salary'] ?? $existingEmployee->getSalary();
        $position = $_POST['position'] ?? $existingEmployee->getPosition();
        $department = $_POST['department'] ?? $existingEmployee->getDepartment();

        if (empty($name) || empty($position) || empty($department)) {
            echo "Все поля обязательны для заполнения";
            return;
        }

        $success = $this->employeeService->updateEmployee($id, $name, $salary, $position, $department);

        if ($success) {
            header('Location: /employees');
        } else {
            echo "Ошибка при обновлении сотрудника";
        }
    }
}