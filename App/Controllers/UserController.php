<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\TemplateEngine;
use App\Services\DepartmentService;
use App\Services\EmployeeService;
use App\Services\UserService;

class UserController
{
    private UserService $userService;
    private EmployeeService $employeeService;

    public function __construct(UserService $userService, EmployeeService $employeeService)
    {
        $this->userService = $userService;
        $this->employeeService = $employeeService;
        $this->templateEngine = new TemplateEngine();
    }

    public function mainPageAction(): void
    {
        $templatePath = __DIR__ . '/../../public/views/main.html';

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $user = null;
        if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
            $user = $this->userService->findByToken($_COOKIE['remember_token']);
            if ($user) {
                $_SESSION['user_id'] = $user->getId();
            }
        } elseif (isset($_SESSION['user_id'])) {
            $user = $this->userService->findById($_SESSION['user_id']);
        }

        echo $this->templateEngine->render($templatePath, ['user' => $user]);
    }

    public function registerFormAction(): void
    {
        $templatePath = __DIR__ . '/../../public/views/user/registration_form.html';
        echo $this->templateEngine->render($templatePath);
    }

    public function registerAction(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $result = $this->userService->processRegistration($input);

        http_response_code($result['status']);
        echo json_encode(['message' => $result['message']]);
    }

    public function verifyEmailAction(): void
    {
        $templatePath = __DIR__ . '/../../public/views/user/email_verified.html';
        $token = $_GET['token'] ?? '';
        if ($token && $this->userService->verifyEmail($token)) {
            echo $this->templateEngine->render($templatePath);
        } else {
            echo "Недействительная или просроченная ссылка.";
        }
    }

    public function loginFormAction(): void
    {
        $templatePath = __DIR__ . '/../../public/views/user/authorization_form.html';
        echo $this->templateEngine->render($templatePath);
    }

    public function loginAction(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $input = json_decode(file_get_contents('php://input'), true);

        $result = $this->userService->processLogin($input);

        http_response_code($result['status']);
        echo json_encode(['message' => $result['message']]);
    }

    public function logoutAction(): void
    {
        if (isset($_SESSION['user_id'])) {
            $this->userService->clearRememberToken((int)$_SESSION['user_id']);
        }

        setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        session_destroy();

        header('Location: /office-manager');
    }

    public function accountAction(): void
    {
        $templatePath = __DIR__ . '/../../public/views/user/account.html';

        $user = null;
        if (isset($_SESSION['user_id'])) {
            $user = $this->userService->findById($_SESSION['user_id']);
        }

        $employee = $this->employeeService->getEmployeeByUserId($user->getId());
        if ($employee) {
            echo $this->templateEngine->render($templatePath, [
                'user' => $user,
                'employee' => $employee]);
        } else {
            echo $this->templateEngine->render($templatePath, ['user' => $user]);
        }
    }

    public function deleteAccountAction(): void
    {
        $userId = (int)$_POST['user_id'] ?? null;
        if (!$userId) {
            http_response_code(400);
            echo "Не указан ID пользователя";
            return;
        }

        $user = $this->userService->findById($userId);
        if (!$user) {
            http_response_code(404);
            echo "Пользователь не найден.";
            return;
        }

        $this->userService->delete($userId);

        session_destroy();
        setcookie('remember_token', '', time() - 3600, '/', '', false, true);

        header('Location: /office-manager');
    }

    public function updateAccountAction(): void
    {
        $id = (int)$_POST['user_id'] ?? null;
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$id || !$firstname) {
            http_response_code(400);
            echo "Недостаточно данных для обновления.";
            return;
        }

        try {
            $this->userService->update($id, $firstname, $lastname, $password);
            exit();
        } catch (\InvalidArgumentException $e) {
            echo $e->getMessage();
        }
        header('Location: /account');
    }
}