<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\TemplateEngine;
use App\Services\UserService;

class UserController
{
    private UserService $userService;
    private TemplateEngine $templateEngine;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->templateEngine = new TemplateEngine();
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
}