<?php

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
        return $stmt->fetchAll(PDO::FETCH_CLASS, User::class);
    }

    public function find(int $id): ?User
    {
        $stmt = $this->entityManager->getConnection()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $user = new User();
        $user->id = $data['id'];
        $user->firstname = $data['firstname'];
        $user->lastname = $data['lastname'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->salt = $data['salt'];
        $user->token = $data['token'];
        $user->time_last_login = $data['time_last_login'];
        $user->is_verified = (bool)$data['is_verified'];

        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->entityManager->getConnection()->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $user = new User();
        $user->id = $data['id'];
        $user->firstname = $data['firstname'];
        $user->lastname = $data['lastname'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->salt = $data['salt'];
        $user->token = $data['token'];
        $user->time_last_login = $data['time_last_login'];
        $user->is_verified = (bool)$data['is_verified'];

        return $user;
    }

    public function add(User $user): bool
    {
        $stmt = $this->entityManager->getConnection()->prepare(
            "INSERT INTO users (id, firstname, lastname, email, password, salt, token, time_last_login, is_verified) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $user->id,
            $user->getFirstname(),
            $user->getLastname(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getSalt(),
            $user->getToken(),
            $user->getTimeLastLogin(),
            $user->isVerified()
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
            $user->getTimeLastLogin(),
            $user->isVerified(),
            $user->id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->entityManager->getConnection()->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}