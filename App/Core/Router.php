<?php
declare(strict_types=1);

namespace App\Core;

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\DepartmentController;
use App\Controllers\EmployeeController;
use App\Controllers\MainController;
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
            '/office-manager' => ['App\Controllers\MainController', 'mainPageAction'],

            '/verify-email' => ['App\Controllers\AuthController', 'verifyEmailAction'],
            '/register-form' => ['App\Controllers\AuthController', 'registerFormAction'],
            '/register' => ['App\Controllers\AuthController', 'registerAction'],
            '/login-form' => ['App\Controllers\AuthController', 'loginFormAction'],
            '/login' => ['App\Controllers\AuthController', 'loginAction'],
            '/logout' => ['App\Controllers\AuthController', 'logoutAction'],
            '/get-salt' => ['App\Controllers\AuthController', 'getSaltAction'],

            '/departments' => ['App\Controllers\DepartmentController', 'listAction'],
            '/departments/delete' => ['App\Controllers\DepartmentController', 'deleteAction'],
            '/departments/add' => ['App\Controllers\DepartmentController', 'addAction'],

            '/employees/department' => ['App\Controllers\EmployeeController', 'listByDepartmentAction'],
            '/employees/delete' => ['App\Controllers\EmployeeController', 'deleteAction'],
            '/employees/add-form' => ['App\Controllers\EmployeeController', 'addFormAction'],
            '/employees/add' => ['App\Controllers\EmployeeController', 'addAction'],
            '/employees/edit-form' => ['App\Controllers\EmployeeController', 'editFormAction'],
            '/employees/edit' => ['App\Controllers\EmployeeController', 'editAction'],

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
        switch ($controllerClass) {
            case MainController::class:
                return new MainController();

            case AuthController::class:
                $repository = new UserRepository($this->entityManager);
                $userService = new UserService($repository);
                return new AuthController($userService, $this->mailService);

            case DepartmentController::class:
                $repository = new DepartmentRepository($this->entityManager);
                $service = new DepartmentService($repository);
                return new DepartmentController($service);

            case EmployeeController::class:
                $departmentRepository = new DepartmentRepository($this->entityManager);
                $departmentService = new DepartmentService($departmentRepository);
                $employeeRepository = new EmployeeRepository($this->entityManager);
                $employeeService = new EmployeeService($employeeRepository, $departmentRepository);
                return new EmployeeController($employeeService, $departmentService);

            case AdminController::class:
                $service = new AdminService();
                return new AdminController($service);

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