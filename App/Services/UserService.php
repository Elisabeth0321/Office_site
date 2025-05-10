<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use DateTimeImmutable;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(string $firstname, string $lastname, string $email, string $clientSideHash, string $salt): bool
    {
        if ($this->userRepository->findByEmail($email)) {
            return false;
        }

        $finalHash = hash('sha256', $clientSideHash);

        $user = new User(
            0,
            $firstname,
            $lastname,
            $email,
            $finalHash,
            $salt,
            '',
            null,
            false
        );

        return $this->userRepository->add($user);
    }

    public function authenticate(string $email, string $clientSideHash): ?User
    {
        $user = $this->userRepository->findByEmail($email);
        if (!$user) return null;

        $expected = hash('sha256', $clientSideHash);

        if ($user->getPassword() !== $expected) return null;

        $user->setTimeLastLogin(new DateTimeImmutable());
        $this->userRepository->update($user);

        return $user;
    }

    public function storeRememberToken(User $user, string $token): void
    {
        $user->setToken($token);
        $this->userRepository->update($user);
    }

    public function clearRememberToken(int $userId): void
    {
        $user = $this->userRepository->find($userId);
        if ($user) {
            $user->setToken('');
            $this->userRepository->update($user);
        }
    }

    public function getUserByToken(string $token): ?User
    {
        return $this->userRepository->findByToken($token);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    public function updateLoginData(int $userId, ?string $token): void
    {
        $this->userRepository->updateLoginData($userId, $token);
    }

    public function sendVerificationEmail(User $user, MailService $mailer): void
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

    public function sendLoginNotification(User $user, MailService $mailer): void
    {
        $subject = "Вы вошли в аккаунт";
        $body = "Здравствуйте, {$user->getFirstname()}!<br><br>
        Мы зафиксировали вход в ваш аккаунт. Если это были не вы — смените пароль.";
        $mailer->send($user->getEmail(), $subject, $body);
    }


}
