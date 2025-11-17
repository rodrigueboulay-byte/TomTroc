<?php

class User
{
    public function __construct(
        private int $id,
        private string $username,
        private ?string $city,
        private ?string $avatarPath,
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
}
