<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\TemplateEngine;
use App\Services\MailService;
use App\Services\UserService;

class AuthController
{
    private UserService $userService;
    private MailService $mailService;
    private TemplateEngine $templateEngine;

    public function __construct(UserService $userService, MailService $mailService)
    {
        $this->userService = $userService;
        $this->mailService = $mailService;
        $this->templateEngine = new TemplateEngine();
        session_start();
    }

    public function registerFormAction(): void
    {
        $templatePath = __DIR__ . '/../../public/views/user/registration_form.html';
        echo $this->templateEngine->render($templatePath);
    }

    public function registerAction(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $firstname = trim($input['firstname'] ?? '');
        $lastname = trim($input['lastname'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $captcha = $input['captcha'] ?? '';

        if (!$firstname || !$email || !$password || !$captcha) {
            http_response_code(400);
            echo json_encode(['message' => 'Все поля обязательны.']);
            return;
        }

        $secretKey = '6LccZjArAAAAAEIYKtf7Z0RzQcSOQ4Zi90v-rH0A';
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha");
        $captchaResult = json_decode($verify, true);
        if (!$captchaResult['success']) {
            http_response_code(400);
            echo json_encode(['message' => 'Ошибка проверки капчи.']);
            return;
        }

        $salt = bin2hex(random_bytes(16));
        $clientHash = hash('sha256', $password . $salt);
        $finalHash = hash('sha256', $clientHash);

        try {
            $success = $this->userService->register($firstname, $lastname, $email, $finalHash, $salt);

            if ($success) {
                $user = $this->userService->findByEmail($email);
                if ($user) {
                    $this->userService->sendVerificationEmail($user, $this->mailService);
                }
                http_response_code(200);
                echo json_encode(['message' => 'Успешная регистрация']);
            } else {
                http_response_code(409);
                echo json_encode(['message' => 'Пользователь уже существует']);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Ошибка сервера: ' . $e->getMessage()]);
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
        $email = trim($input['email'] ?? '');
        $clientHash = $input['loginHash'] ?? '';
        $rememberMe = $input['remember_me'] ?? false;
        //$token = $input['rememberToken'] ?? null;
        $captcha = $input['captcha'] ?? '';

        if (!$email || !$clientHash || !$captcha) {
            http_response_code(400);
            echo json_encode(['message' => 'Не все поля заполнены']);
            return;
        }

        $secretKey = '6LccZjArAAAAAEIYKtf7Z0RzQcSOQ4Zi90v-rH0A';
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha");
        $captchaResult = json_decode($verify, true);
        if (!$captchaResult['success']) {
            http_response_code(400);
            echo json_encode(['message' => 'Ошибка проверки капчи']);
            return;
        }

        $user = $this->userService->findByEmail($email);
        if (!$user) {
            http_response_code(401);
            echo json_encode(['message' => 'Пользователь не найден']);
            return;
        }

        if ($clientHash !== $user->getPassword()) {
            http_response_code(401);
            echo json_encode(['message' => 'Неверный пароль']);
            return;
        }

        $this->userService->updateLoginData($user->getId(), '1234');

        session_start();
        $_SESSION['user_id'] = $user->getId();

        http_response_code(200);
        echo json_encode(['message' => 'Успешная авторизация']);
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

}
