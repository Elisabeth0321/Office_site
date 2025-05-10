<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use DateTimeImmutable;

class UserService
{
    private UserRepository $userRepository;
    private MailService $mailService;

    public function __construct(UserRepository $userRepository, MailService $mailService)
    {
        $this->userRepository = $userRepository;
        $this->mailService = $mailService;
    }

    public function processRegistration(array $input): array
    {
        $firstname = trim($input['firstname'] ?? '');
        $lastname = trim($input['lastname'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $captcha = $input['captcha'] ?? '';

        if (!$firstname || !$email || !$password || !$captcha) {
            return ['status' => 400, 'message' => 'Все поля обязательны.'];
        }

        if (!$this->verifyCaptcha($captcha)) {
            return ['status' => 400, 'message' => 'Ошибка проверки капчи.'];
        }

        $salt = bin2hex(random_bytes(16));
        $hashedPassword = hash('sha256', $password . $salt);

        $success = $this->register($firstname, $lastname, $email, $hashedPassword, $salt);

        if (!$success) {
            return ['status' => 409, 'message' => 'Пользователь уже существует'];
        }

        $user = $this->findByEmail($email);
        if ($user) {
            $this->sendVerificationEmail($user, $this->mailService);
        }

        return ['status' => 200, 'message' => 'Успешная регистрация'];
    }

    public function processLogin(array $input): array
    {
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $rememberMe = $input['rememberMe'] ?? false;
        $captcha = $input['captcha'] ?? '';

        if (!$email || !$password || !$captcha) {
            return ['status' => 400, 'message' => 'Не все поля заполнены'];
        }

        if (!$this->verifyCaptcha($captcha)) {
            return ['status' => 400, 'message' => 'Ошибка проверки капчи'];
        }

        $user = $this->findByEmail($email);
        if (!$user) {
            return ['status' => 401, 'message' => 'Пользователь не найден'];
        }

        $salt = $user->getSalt();
        $hashedPassword = hash('sha256', $password . $salt);
        if ($hashedPassword !== $user->getPassword()) {
            return ['status' => 401, 'message' => 'Неверный пароль'];
        }

        $_SESSION['user_id'] = $user->getId();

        $user->setTimeLastLogin(new DateTimeImmutable());

        if ($rememberMe) {
            $token = bin2hex(random_bytes(32));
            $user->setToken($token);

            if (headers_sent($file, $line)) {
                error_log("Headers already sent in $file on line $line");
            }
            setcookie('remember_token', $token, [
                'expires' => time() + (86400 * 30),
                'path' => '/',
                'httponly' => true,
                'secure' => false,
                'samesite' => 'Lax'
            ]);
        } else {
            $user->setToken('');
        }

        $this->userRepository->update($user);

        $this->sendLoginNotification($user, $this->mailService);

        return ['status' => 200, 'message' => 'Успешная авторизация'];
    }

    private function register(string $firstname, string $lastname, string $email, string $passwordHash, string $salt): bool
    {
        if ($this->userRepository->findByEmail($email)) {
            return false;
        }

        $user = new User(
            0,
            $firstname,
            $lastname,
            $email,
            $passwordHash,
            $salt,
            '',
            null,
            false
        );

        return $this->userRepository->add($user);
    }

    private function verifyCaptcha(string $token): bool
    {
        $secretKey = '6LccZjArAAAAAEIYKtf7Z0RzQcSOQ4Zi90v-rH0A';
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$token}");
        $result = json_decode($verify, true);

        return $result['success'] ?? false;
    }

    public function clearRememberToken(int $userId): void
    {
        $user = $this->userRepository->findById($userId);
        if ($user) {
            $user->setToken('');
            $this->userRepository->update($user);
        }
    }

    public function findById(int $userId): ?User
    {
        return $this->userRepository->findById($userId);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    public function findByToken(string $remember_token): ?User
    {
        return $this->userRepository->findByToken($remember_token);
    }

    public function updateLoginData(int $userId, ?string $token): void
    {
        $this->userRepository->updateLoginData($userId, $token);
    }

    private function sendVerificationEmail(User $user, MailService $mailer): void
    {
        $token = bin2hex(random_bytes(32));
        $user->setToken($token);
        $this->userRepository->update($user);

        $link = "http://localhost/verify-email?token=" . $token;
        $subject = "Подтверждение регистрации";
        $body = "Здравствуйте, {$user->getFirstname()}!<br><br>
        Пожалуйста, подтвердите вашу регистрацию, перейдя по ссылке:<br>
        <a href=\"$link\">$link</a>";

        $mailer->send($user->getEmail(), $subject, $body);
    }

    public function verifyEmail(string $token): bool
    {
        $user = $this->userRepository->findByToken($token);
        if (!$user) return false;

        $user->setVerified(true);
        $user->setToken('');
        return $this->userRepository->update($user);
    }

    private function sendLoginNotification(User $user, MailService $mailer): void
    {
        $subject = "Вы вошли в аккаунт";
        $body = "Здравствуйте, {$user->getFirstname()}!<br><br>
        Мы зафиксировали вход в ваш аккаунт. Если это были не вы — смените пароль.";
        $mailer->send($user->getEmail(), $subject, $body);
    }




}
