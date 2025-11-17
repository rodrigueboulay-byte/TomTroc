<?php

class User
{
    public function __construct(
        private int $id,
        private string $username,
        private ?string $city = null,
        private ?string $avatarPath = null,
        private ?string $email = null,
        private ?string $passwordHash = null,
        private ?string $bio = null,
        private ?string $roles = null,
        private ?bool $isActive = null,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getAvatarPath(): ?string
    {
        return $this->avatarPath;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function getRoles(): ?string
    {
        return $this->roles;
    }

    public function isActive(): bool
    {
        return $this->isActive ?? true;
    }
}
