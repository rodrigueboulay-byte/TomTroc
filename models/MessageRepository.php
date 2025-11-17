<?php

class MessageRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * @return array<int, array{partner: User, lastMessage: Message, unread: int, cover?: ?string}>
     */
    public function getConversationsForUser(int $userId): array
    {
        $sql = <<<SQL
            SELECT
                m.id,
                m.sender_id,
                m.receiver_id,
                m.content,
                m.created_at,
                m.is_read,
                m.exchange_id,
                sender.username AS sender_username,
                sender.email AS sender_email,
                sender.city AS sender_city,
                sender.avatar_path AS sender_avatar,
                receiver.username AS receiver_username,
                receiver.email AS receiver_email,
                receiver.city AS receiver_city,
                receiver.avatar_path AS receiver_avatar,
                requested_book.cover_image_path AS cover_image_path
            FROM message m
            INNER JOIN user sender ON sender.id = m.sender_id
            INNER JOIN user receiver ON receiver.id = m.receiver_id
            LEFT JOIN exchange_request er ON er.id = m.exchange_id
            LEFT JOIN book requested_book ON requested_book.id = er.requested_book_id
            WHERE m.sender_id = :userId OR m.receiver_id = :userId
            ORDER BY m.created_at DESC, m.id DESC
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        $map = [];
        $order = [];

        foreach ($rows as $row) {
            $message = $this->hydrateMessage($row);
            $partner = $message->getSender()->getId() === $userId ? $message->getReceiver() : $message->getSender();
            $partnerId = $partner->getId();

            if (!isset($map[$partnerId])) {
                $cover = null;
                if (!empty($row['exchange_id']) && $row['exchange_id'] !== null) {
                    $cover = $row['cover_image_path'] ?? null;
                }

                $map[$partnerId] = [
                    'partner' => $partner,
                    'lastMessage' => $message,
                    'unread' => 0,
                    'cover' => $cover,
                ];
                $order[] = $partnerId;
            }

            if ($message->getReceiver()->getId() === $userId && !$message->isRead()) {
                $map[$partnerId]['unread']++;
            }
        }

        $conversations = [];
        foreach ($order as $partnerId) {
            $conversations[] = $map[$partnerId];
        }

        return $conversations;
    }

    /**
     * @return Message[]
     */
    public function getConversationMessages(int $userId, int $partnerId): array
    {
        $sql = <<<SQL
            SELECT
                m.id,
                m.sender_id,
                m.receiver_id,
                m.content,
                m.created_at,
                m.is_read,
                m.exchange_id,
                sender.username AS sender_username,
                sender.email AS sender_email,
                sender.city AS sender_city,
                sender.avatar_path AS sender_avatar,
                receiver.username AS receiver_username,
                receiver.email AS receiver_email,
                receiver.city AS receiver_city,
                receiver.avatar_path AS receiver_avatar
            FROM message m
            INNER JOIN user sender ON sender.id = m.sender_id
            INNER JOIN user receiver ON receiver.id = m.receiver_id
            WHERE (m.sender_id = :userId AND m.receiver_id = :partnerId)
                OR (m.sender_id = :partnerId AND m.receiver_id = :userId)
            ORDER BY m.created_at ASC, m.id ASC
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':partnerId', $partnerId, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(fn ($row) => $this->hydrateMessage($row), $stmt->fetchAll());
    }

    public function markConversationAsRead(int $userId, int $partnerId): void
    {
        $sql = <<<SQL
            UPDATE message
            SET is_read = 1
            WHERE receiver_id = :userId
              AND sender_id = :partnerId
              AND is_read = 0
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':userId' => $userId,
            ':partnerId' => $partnerId,
        ]);
    }

    public function sendMessage(int $senderId, int $receiverId, string $content, ?int $exchangeId = null): void
    {
        $sql = <<<SQL
            INSERT INTO message (sender_id, receiver_id, content, exchange_id)
            VALUES (:sender_id, :receiver_id, :content, :exchange_id)
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':sender_id' => $senderId,
            ':receiver_id' => $receiverId,
            ':content' => $content,
            ':exchange_id' => $exchangeId,
        ]);
    }

    public function countUnreadForUser(int $userId): int
    {
        $sql = 'SELECT COUNT(*) FROM message WHERE receiver_id = :userId AND is_read = 0';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    private function hydrateMessage(array $row): Message
    {
        $sender = new User(
            (int) $row['sender_id'],
            $row['sender_username'],
            $row['sender_city'] ?? null,
            $row['sender_avatar'] ?? null,
            $row['sender_email'] ?? null
        );

        $receiver = new User(
            (int) $row['receiver_id'],
            $row['receiver_username'],
            $row['receiver_city'] ?? null,
            $row['receiver_avatar'] ?? null,
            $row['receiver_email'] ?? null
        );

        return new Message(
            (int) $row['id'],
            $sender,
            $receiver,
            $row['content'],
            new DateTimeImmutable($row['created_at']),
            (bool) $row['is_read'],
            isset($row['exchange_id']) ? (int) $row['exchange_id'] : null
        );
    }
}
