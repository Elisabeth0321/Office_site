<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\TemplateEngine;
use App\Services\MailService;
use App\Services\UserService;

class AuthController
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
        $token = $_GET['token'] ?? '';
        if ($token && $this->userService->verifyEmail($token)) {
            $templatePath = __DIR__ . '/../../public/views/user/email_verified.html';
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

    public function getSaltAction(): void
    {
        $email = trim($_GET['email'] ?? '');

        if (!$email) {
            http_response_code(400);
            echo json_encode(['message' => 'Email не указан']);
            return;
        }

        $user = $this->userService->findByEmail($email);

        if (!$user) {
            http_response_code(404);
            echo json_encode(['message' => 'Пользователь не найден']);
            return;
        }

        echo json_encode(['salt' => $user->getSalt()]);
    }
}