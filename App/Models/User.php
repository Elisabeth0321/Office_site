<?php
declare(strict_types = 1);

namespace App\Models;

class User
{
    private int $id;
    private string $firstname;
    private string $lastname;
    private string $email;
    private string $password;
    private string $salt;
    private ?string $token = null;
    private ?\DateTimeImmutable $timeLastLogin = null;
    private bool $isVerified = false;
    private ?Employee $employee = null;

    public function __construct(
        int $id,
        string $firstname,
        string $lastname,
        string $email,
        string $password,
        string $salt,
        bool $isVerified = false
    ) {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->password = $password;
        $this->salt = $salt;
        $this->isVerified = $isVerified;
    }

    public function getId(): int { return $this->id; }
    public function getFirstname(): string { return $this->firstname; }
    public function getLastname(): string { return $this->lastname; }
    public function getEmail(): string { return $this->email; }
    public function getPassword(): string { return $this->password; }
    public function getSalt(): string { return $this->salt; }
    public function getToken(): ?string { return $this->token; }
    public function getTimeLastLogin(): ?\DateTimeImmutable { return $this->timeLastLogin; }
    public function isVerified(): bool { return $this->isVerified; }
    public function getEmployee(): ?Employee { return $this->employee; }

    public function setToken(?string $token): void { $this->token = $token; }
    public function setTimeLastLogin(\DateTimeImmutable $time): void { $this->timeLastLogin = $time; }
    public function setVerified(bool $v): void { $this->isVerified = $v; }

    public function setEmployee(Employee $employee): void
    {
        $this->employee = $employee;
        $employee->setUser($this);
    }
}