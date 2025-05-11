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
    private TemplateEngine $templateEngine;

    public function __construct(UserService $userService, EmployeeService $employeeService)
    {
        $this->userService = $userService;
        $this->employeeService = $employeeService;
        $this->templateEngine = new TemplateEngine();
    }

    public function mainPageAction(): void
    {
        $templatePath = __DIR__ . '/../../public/views/main.html';

        try {
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
        } catch (\Throwable $e) {
            error_log('Ошибка в mainPageAction: ' . $e->getMessage());
            http_response_code(500);
            echo "Произошла внутренняя ошибка.";
        }
    }

    public function registerFormAction(): void
    {
        $templatePath = __DIR__ . '/../../public/views/user/registration_form.html';
        echo $this->templateEngine->render($templatePath);
    }

    public function registerAction(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $result = $this->userService->processRegistration($input);

            http_response_code($result['status']);
            echo json_encode(['message' => $result['message']]);
        } catch (\Throwable $e) {
            error_log('Ошибка в registerAction: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['message' => 'Ошибка регистрации.']);
        }
    }

    public function verifyEmailAction(): void
    {
        $templatePath = __DIR__ . '/../../public/views/user/email_verified.html';

        try {
            $token = $_GET['token'] ?? '';
            if ($token && $this->userService->verifyEmail($token)) {
                echo $this->templateEngine->render($templatePath);
            } else {
                echo "Недействительная или просроченная ссылка.";
            }
        } catch (\Throwable $e) {
            error_log('Ошибка в verifyEmailAction: ' . $e->getMessage());
            http_response_code(500);
            echo "Произошла ошибка при подтверждении.";
        }
    }

    public function loginFormAction(): void
    {
        $templatePath = __DIR__ . '/../../public/views/user/authorization_form.html';
        echo $this->templateEngine->render($templatePath);
    }

    public function loginAction(): void
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $result = $this->userService->processLogin($input);

            http_response_code($result['status']);
            echo json_encode(['message' => $result['message']]);
        } catch (\Throwable $e) {
            error_log('Ошибка в loginAction: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['message' => 'Ошибка авторизации.']);
        }
    }

    public function logoutAction(): void
    {
        try {
            if (isset($_SESSION['user_id'])) {
                $this->userService->clearRememberToken((int)$_SESSION['user_id']);
            }

            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
            session_destroy();

            header('Location: /office-manager');
        } catch (\Throwable $e) {
            error_log('Ошибка в logoutAction: ' . $e->getMessage());
            http_response_code(500);
            echo "Ошибка при выходе.";
        }
    }

    public function accountAction(): void
    {
        $templatePath = __DIR__ . '/../../public/views/user/account.html';

        try {
            $user = null;
            if (isset($_SESSION['user_id'])) {
                $user = $this->userService->findById($_SESSION['user_id']);
            }

            $employee = $this->employeeService->getEmployeeByUserId($user->getId());

            echo $this->templateEngine->render($templatePath, [
                'user' => $user,
                'employee' => $employee ?: null
            ]);
        } catch (\Throwable $e) {
            error_log('Ошибка в accountAction: ' . $e->getMessage());
            http_response_code(500);
            echo "Ошибка при загрузке профиля.";
        }
    }

    public function deleteAccountAction(): void
    {
        $userId = (int)($_POST['user_id'] ?? 0);
        if (!$userId) {
            http_response_code(400);
            echo "Не указан ID пользователя";
            return;
        }

        try {
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
            exit();
        } catch (\Throwable $e) {
            error_log('Ошибка в deleteAccountAction: ' . $e->getMessage());
            http_response_code(500);
            echo "Ошибка при удалении аккаунта.";
        }
    }

    public function updateAccountAction(): void
    {
        $id = (int)($_POST['user_id'] ?? 0);
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
            header('Location: /account');
            exit();
        } catch (\Throwable $e) {
            error_log('Ошибка в updateAccountAction: ' . $e->getMessage());
            http_response_code(500);
            echo "Ошибка при обновлении профиля.";
        }
    }
}