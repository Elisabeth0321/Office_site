<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\TemplateEngine;
use App\Services\DepartmentService;
use App\Services\EmployeeService;

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

    public function listAction(): void
    {
        $employees = $this->employeeService->getAllEmployees();

        echo $this->templateEngine->render(
            __DIR__ . '/../../public/views/employee/employee_list.html',
            ['employees' => $employees]
        );
    }

    public function listByDepartmentAction(): void
    {
        $deptId = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if (!$deptId) {
            echo "ID отдела не указан";
            return;
        }

        $department = $this->departmentService->getDepartmentById($deptId);
        if (!$department) {
            echo "Отдел не найден";
            return;
        }

        $employees = $this->employeeService->getEmployeesByDepartment($deptId);

        echo $this->templateEngine->render(
            __DIR__ . '/../../public/views/employee/employee_list.html',
            [
                'department' => $department,
                'employees' => $employees,
            ]
        );
    }

    public function deleteAction(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $deptId = isset($_GET['departmentId']) ? (int)$_GET['departmentId'] : null;
        if (!$id) {
            echo "ID сотрудника не указан";
            return;
        }

        $this->employeeService->deleteEmployee($id);
        header("Location: /employees/department?id={$deptId}");
        exit();
    }

    public function addFormAction(): void
    {
        $departmentId = isset($_GET['departmentId']) ? (int)$_GET['departmentId'] : null;
        if ($departmentId === null) {
            echo "ID отдела не указан";
            return;
        }

        echo $this->templateEngine->render(
            __DIR__ . '/../../public/views/employee/employee_add_form.html',
            ['departmentId' => $departmentId]
        );
    }

    public function addAction(): void
    {
        $name = $_POST['name'] ?? '';
        $salary = isset($_POST['salary']) ? (float)$_POST['salary'] : 0.0;
        $position = $_POST['position'] ?? '';
        $deptId = isset($_POST['departmentId']) ? (int)$_POST['departmentId'] : 0;

        if ($name === '' || $position === '') {
            echo "Все поля обязательны для заполнения";
            return;
        }

        try {
            $this->employeeService->addEmployee($name, $salary, $position, $deptId);
            header("Location: /employees/department?id={$deptId}");
            exit();
        } catch (\InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }

    public function editFormAction(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if (!$id) {
            echo "ID не указан";
            return;
        }

        $employee = $this->employeeService->getEmployeeById($id);
        $departments = $this->departmentService->getAllDepartments();

        if (!$employee) {
            echo "Сотрудник не найден";
            return;
        }

        echo $this->templateEngine->render(
            __DIR__ . '/../../public/views/employee/employee_edit_form.html',
            [
                'employee' => $employee,
                'departments' => $departments,
            ]
        );
    }

    public function editAction(): void
    {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
        if (!$id) {
            echo "ID не указан";
            return;
        }

        $name = $_POST['name'] ?? '';
        $salary = isset($_POST['salary']) ? (float)$_POST['salary'] : 0.0;
        $position = $_POST['position'] ?? '';
        $deptId = isset($_POST['departmentId']) ? (int)$_POST['departmentId'] : 0;

        if ($name === '' || $position === '') {
            echo "Все поля обязательны для заполнения";
            return;
        }

        try {
            $this->employeeService->updateEmployee($id, $name, $salary, $position, $deptId);
            header("Location: /employees/department?id={$deptId}");
            exit();
        } catch (\InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
