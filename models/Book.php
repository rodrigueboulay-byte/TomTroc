<?php

class Book
{
    public function __construct(
        private int $id,
        private string $title,
        private string $author,
        private ?string $description,
        private string $condition,
        private bool $isAvailable,
        private ?string $coverImagePath,
        private DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $updatedAt,
        private User $owner,
        private ?Genre $genre,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCondition(): string
    {
        return $this->condition;
    }

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    public function getCoverImagePath(): ?string
    {
        return $this->coverImagePath;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }
}
