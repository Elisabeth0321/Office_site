<?php

namespace App\Models;

class User
{
    public int $id;
    public string $firstname;
    public string $lastname;
    public string $email;
    public string $password;
    public string $salt;
    public ?string $token = null;
    public ?string $time_last_login = null;
    public bool $is_verified = false;

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSalt(): string
    {
        return $this->salt;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getTimeLastLogin(): ?string
    {
        return $this->time_last_login;
    }

    public function isVerified(): bool
    {
        return $this->is_verified;
    }
}
