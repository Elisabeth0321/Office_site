<?php

namespace App\Core;

use App\Repositories\EmployeeRepository;
use App\Services\EmployeeService;

class Router {
    private array $routes;
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;

        $this->routes = [
            '/employees' => ['App\Controllers\EmployeeController', 'listAction'],
            '/employees/view' => ['App\Controllers\EmployeeController', 'viewAction'],
            '/employees/delete' => ['App\Controllers\EmployeeController', 'deleteAction'],
            '/employees/add-form' => ['App\Controllers\EmployeeController', 'addFormAction'],
            '/employees/add' => ['App\Controllers\EmployeeController', 'addAction'],
            '/employees/edit-form' => ['App\Controllers\EmployeeController', 'editFormAction'],
            '/employees/edit' => ['App\Controllers\EmployeeController', 'editAction']
        ];
    }

    public function dispatch(string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH);

        if (array_key_exists($path, $this->routes)) {
            [$controllerClass, $method] = $this->routes[$path];
            $repository = new EmployeeRepository($this->entityManager);
            $service = new EmployeeService($repository);
            $controller = new $controllerClass($service);
            call_user_func([$controller, $method]);
        } else {
            http_response_code(404);
            echo "Страница не найдена";
        }
    }

}