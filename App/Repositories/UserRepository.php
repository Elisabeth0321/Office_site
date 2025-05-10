<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\EntityManager;
use App\Models\User;
use PDO;

class UserRepository
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findAll(): array
    {
        $stmt = $this->entityManager->getConnection()->query("SELECT * FROM users");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'mapRowToUser'], $results);
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->entityManager->getConnection()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapRowToUser($row) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->entityManager->getConnection()->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapRowToUser($row) : null;
    }

    public function findByToken(string $token): ?User
    {
        $stmt = $this->entityManager->getConnection()->prepare("SELECT * FROM users WHERE token = ?");
        $stmt->execute([$token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapRowToUser($row) : null;
    }

    public function add(User $user): bool
    {
        $stmt = $this->entityManager->getConnection()->prepare(
            "INSERT INTO users (firstname, lastname, email, password, salt, token, time_last_login, is_verified)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $user->getFirstname(),
            $user->getLastname(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getSalt(),
            $user->getToken(),
            $user->getTimeLastLogin()?->format('Y-m-d H:i:s'),
            (int) $user->isVerified()
        ]);
    }

    public function update(User $user): bool
    {
        $stmt = $this->entityManager->getConnection()->prepare(
            "UPDATE users SET firstname = ?, lastname = ?, email = ?, password = ?, salt = ?, token = ?, time_last_login = ?, is_verified = ?
             WHERE id = ?"
        );
        return $stmt->execute([
            $user->getFirstname(),
            $user->getLastname(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getSalt(),
            $user->getToken(),
            $user->getTimeLastLogin()?->format('Y-m-d H:i:s'),
            (int) $user->isVerified(),
            $user->getId()
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->entityManager->getConnection()->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    private function mapRowToUser(array $row): User
    {
        return new User(
            (int)$row['id'],
            $row['firstname'],
            $row['lastname'],
            $row['email'],
            $row['password'],
            $row['salt'],
            $row['token'] ?? null,
            $row['time_last_login'] ?? null,
            (bool) $row['is_verified']
        );
    }

    public function updateLoginData(int $userId, ?string $token): bool
    {
        $stmt = $this->entityManager->getConnection()->prepare(
            "UPDATE users SET token = ?, time_last_login = NOW() WHERE id = ?"
        );
        return $stmt->execute([$token, $userId]);
    }

}
