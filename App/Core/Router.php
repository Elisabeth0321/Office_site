<?php
declare(strict_types=1);

namespace App\Core;

use App\Controllers\AdminController;
use App\Controllers\UserController;
use App\Controllers\DepartmentController;
use App\Controllers\EmployeeController;
use App\Repositories\DepartmentRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\UserRepository;
use App\Services\AdminService;
use App\Services\DepartmentService;
use App\Services\EmployeeService;
use App\Services\MailService;
use App\Services\UserService;

class Router
{
    private array $routes;
    private EntityManager $entityManager;
    private MailService $mailService;

    public function __construct(EntityManager $entityManager, MailService $mailService)
    {
        $this->entityManager = $entityManager;
        $this->mailService = $mailService;
        $this->routes = [
            '/office-manager' => ['App\Controllers\UserController', 'mainPageAction'],

            '/account' => ['App\Controllers\UserController', 'accountAction'],
            '/account/update' => ['App\Controllers\UserController', 'updateAccountAction'],
            '/account/delete' => ['App\Controllers\UserController', 'deleteAccountAction'],

            '/register-form' => ['App\Controllers\UserController', 'registerFormAction'],
            '/register' => ['App\Controllers\UserController', 'registerAction'],
            '/verify-email' => ['App\Controllers\UserController', 'verifyEmailAction'],
            '/login-form' => ['App\Controllers\UserController', 'loginFormAction'],
            '/login' => ['App\Controllers\UserController', 'loginAction'],
            '/logout' => ['App\Controllers\UserController', 'logoutAction'],

            '/departments' => ['App\Controllers\DepartmentController', 'listAction'],
            '/departments/delete' => ['App\Controllers\DepartmentController', 'deleteAction'],
            '/departments/add' => ['App\Controllers\DepartmentController', 'addAction'],

            '/employee/update' => ['App\Controllers\EmployeeController', 'updateAction'],
            '/employees/department' => ['App\Controllers\EmployeeController', 'listByDepartmentAction'],
            '/employee/delete' => ['App\Controllers\EmployeeController', 'deleteAction'],
            '/employee/add-form' => ['App\Controllers\EmployeeController', 'addFormAction'],
            '/employee/add' => ['App\Controllers\EmployeeController', 'addAction'],
            '/employee/edit-form' => ['App\Controllers\EmployeeController', 'editFormAction'],
            '/employee/edit' => ['App\Controllers\EmployeeController', 'editAction'],

            '/admin' => ['App\Controllers\AdminController', 'indexAction'],
            '/admin/files' => ['App\Controllers\AdminController', 'indexAction'],
            '/admin/files/upload' => ['App\Controllers\AdminController', 'uploadAction'],
            '/admin/files/download' => ['App\Controllers\AdminController', 'downloadAction'],
            '/admin/files/delete' => ['App\Controllers\AdminController', 'deleteAction'],
            '/admin/files/edit' => ['App\Controllers\AdminController', 'editAction'],
            '/admin/files/mkdir' => ['App\Controllers\AdminController', 'createDirectoryAction'],
        ];
    }

    public function dispatch(string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH);

        if (!array_key_exists($path, $this->routes)) {
            $this->notFound();
            return;
        }

        [$controllerClass, $method] = $this->routes[$path];

        if (!class_exists($controllerClass) || !method_exists($controllerClass, $method)) {
            $this->notFound();
            return;
        }

        $controller = $this->createController($controllerClass);
        $controller->$method();
    }

    private function createController(string $controllerClass): object
    {
        $userRepository = new UserRepository($this->entityManager);
        $employeeRepository = new EmployeeRepository($this->entityManager);
        $departmentRepository = new DepartmentRepository($this->entityManager);

        $userService = new UserService($userRepository, $this->mailService);
        $employeeService = new EmployeeService($employeeRepository, $departmentRepository, $userRepository);
        $departmentService = new DepartmentService($departmentRepository);

        switch ($controllerClass) {
            case UserController::class:
                return new UserController($userService, $employeeService);
            case DepartmentController::class:
                return new DepartmentController($departmentService);
            case EmployeeController::class:
               return new EmployeeController($employeeService, $departmentService);
            case AdminController::class:
                $adminService = new AdminService();
                return new AdminController($adminService);
            default:
                throw new \RuntimeException("Unknown controller: {$controllerClass}");
        }
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo "Страница не найдена";
    }
}