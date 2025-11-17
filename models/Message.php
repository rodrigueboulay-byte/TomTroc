<?php

class Message
{
    public function __construct(
        private int $id,
        private User $sender,
        private User $receiver,
        private string $content,
        private DateTimeImmutable $createdAt,
        private bool $isRead,
        private ?int $exchangeId = null,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSender(): User
    {
        return $this->sender;
    }

    public function getReceiver(): User
    {
        return $this->receiver;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }

    public function getExchangeId(): ?int
    {
        return $this->exchangeId;
    }
}
