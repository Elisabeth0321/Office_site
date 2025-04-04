<?php

namespace App\Controllers;

use App\Core\TemplateEngine;
use App\Services\DepartmentService;
use App\Services\EmployeeService;

class EmployeeController
{
    private EmployeeService $employeeService;
    private DepartmentService $departmentService;

    public function __construct(EmployeeService $employeeService, DepartmentService $departmentService)
    {
        $this->employeeService = $employeeService;
        $this->departmentService = $departmentService;
    }

    public function listAction(): void
    {
        $employees = $this->employeeService->getAllEmployees();

        $templatePath = __DIR__ . '/../../public/views/employee/employee_list.html';
        $templateEngine = new TemplateEngine();
        echo $templateEngine->render($templatePath, [
            'employees' => $employees
        ]);
    }

    public function listByDepartmentAction(): void
    {
        $departmentId = $_GET['id'] ?? null;
        if (!$departmentId) {
            echo "ID отдела не указан";
            return;
        }

        $employees = $this->employeeService->getEmployeesByDepartment($departmentId);
        $department = $this->departmentService->getDepartmentById($departmentId);

        if (!$department) {
            echo "Отдел не найден";
            return;
        }

        $templatePath = __DIR__ . '/../../public/views/employee/employee_list.html';
        $templateEngine = new TemplateEngine();
        echo $templateEngine->render($templatePath, [
            'employees' => $employees,
            'department' => $department
        ]);
    }

    public function deleteAction(): void
    {
        $id = $_GET['id'] ?? null;
        $departmentId = $_GET['departmentId'] ?? null;

        $success = $this->employeeService->deleteEmployee($id);

        if ($success) {
            header("Location: /employees/department?id={$departmentId}");
        } else {
            echo "Ошибка при удалении сотрудника";
        }
    }

    public function addFormAction(): void
    {
        $departmentId = $_GET['departmentId'] ?? null;

        $templatePath = __DIR__ . '/../../public/views/employee/employee_add_form.html';
        $templateEngine = new TemplateEngine();
        echo $templateEngine->render($templatePath, [
            'departmentId' => $departmentId
        ]);
    }

    public function addAction(): void
    {
        $name = $_POST['name'] ?? '';
        $salary = $_POST['salary'] ?? 0;
        $position = $_POST['position'] ?? '';
        $departmentId = isset($_POST['departmentId']) ? (int) $_POST['departmentId'] : 0;

        if (empty($name) || empty($position)) {
            echo "Все поля обязательны для заполнения";
            return;
        }

        $success = $this->employeeService->addEmployee($name, $salary, $position, $departmentId);

        if ($success) {
            header("Location: /employees/department?id={$departmentId}");
            exit();
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

        $departments = $this->departmentService->getAllDepartments();

        $templatePath = __DIR__ . '/../../public/views/employee/employee_edit_form.html';
        $templateEngine = new TemplateEngine();
        echo $templateEngine->render($templatePath, [
            'employee' => $employee,
            'departments' => $departments
        ]);
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
        $departmentId = $_POST['departmentId'] ?? $existingEmployee->getDepartmentId();

        if (empty($name) || empty($position)) {
            echo "Все поля обязательны для заполнения";
            return;
        }

        $success = $this->employeeService->updateEmployee($id, $name, $salary, $position, $departmentId);

        if ($success) {
            header("Location: /employees/department?id={$departmentId}");
        } else {
            echo "Ошибка при обновлении сотрудника";
        }
    }
}