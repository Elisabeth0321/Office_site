<?php

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

    public function register(User $user): bool
    {
        $salt = bin2hex(random_bytes(16));
        $hashedPassword = hash('sha256', $user->getPassword() . $salt);

        $user->setSalt($salt);
        $user->setPassword($hashedPassword);
        $user->setVerified(false);

        return $this->userRepository->add($user);
    }

    public function login(string $email, string $plainPassword): ?User
    {
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            return null;
        }

        $hashedPassword = hash('sha256', $plainPassword . $user->getSalt());
        if ($hashedPassword !== $user->getPassword()) {
            return null;
        }

        $now = new DateTimeImmutable();
        $user->setTimeLastLogin($now);
        $this->userRepository->update($user);

        return $user;
    }

    public function verifyUser(int $id): bool
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return false;
        }

        $user->setVerified(true);
        return $this->userRepository->update($user);
    }

    public function getUserById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function deleteUser(int $id): bool
    {
        return $this->userRepository->delete($id);
    }
}
