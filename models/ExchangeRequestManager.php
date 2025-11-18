<?php

class ExchangeRequestManager
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    public function create(int $requesterId, int $requestedId, int $offeredBookId, int $requestedBookId): int
    {
        $sql = <<<'SQL'
            INSERT INTO exchange_request (requester_id, requested_id, offered_book_id, requested_book_id)
            VALUES (:requester_id, :requested_id, :offered_book_id, :requested_book_id)
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':requester_id' => $requesterId,
            ':requested_id' => $requestedId,
            ':offered_book_id' => $offeredBookId,
            ':requested_book_id' => $requestedBookId,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * @return array{
     *     id:int,
     *     requester_id:int,
     *     requester_name:string,
     *     requested_id:int,
     *     requested_name:string,
     *     offered_book_id:int,
     *     offered_book_title:string,
     *     offered_book_cover:?string,
     *     requested_book_id:int,
     *     requested_book_title:string,
     *     requested_book_cover:?string
     * }|null
     */
    public function findSummary(int $exchangeRequestId): ?array
    {
        $sql = <<<'SQL'
            SELECT
                er.id,
                er.requester_id,
                er.requested_id,
                er.offered_book_id,
                er.requested_book_id,
                requester.username AS requester_name,
                requested.username AS requested_name,
                offered.title AS offered_book_title,
                offered.cover_image_path AS offered_book_cover,
                requested_book.title AS requested_book_title,
                requested_book.cover_image_path AS requested_book_cover
            FROM exchange_request er
            INNER JOIN user requester ON requester.id = er.requester_id
            INNER JOIN user requested ON requested.id = er.requested_id
            INNER JOIN book offered ON offered.id = er.offered_book_id
            INNER JOIN book requested_book ON requested_book.id = er.requested_book_id
            WHERE er.id = :exchangeRequestId
            LIMIT 1
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':exchangeRequestId', $exchangeRequestId, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        if ($row === false) {
            return null;
        }

        return [
            'id' => (int) $row['id'],
            'requester_id' => (int) $row['requester_id'],
            'requester_name' => $row['requester_name'],
            'requested_id' => (int) $row['requested_id'],
            'requested_name' => $row['requested_name'],
            'offered_book_id' => (int) $row['offered_book_id'],
            'offered_book_title' => $row['offered_book_title'],
            'offered_book_cover' => $row['offered_book_cover'] ?: null,
            'requested_book_id' => (int) $row['requested_book_id'],
            'requested_book_title' => $row['requested_book_title'],
            'requested_book_cover' => $row['requested_book_cover'] ?: null,
        ];
    }

    /**
     * @return array{type:string,status:string,requested_book_title:string,offered_book_title:string,other_user:string,created_at:string}[]
     */
    public function getRequestsForUser(int $userId): array
    {
        $sql = <<<'SQL'
            SELECT
                er.id,
                er.requester_id,
                er.requested_id,
                er.status,
                er.created_at,
                requester.username AS requester_name,
                requested.username AS requested_name,
                offered.title AS offered_title,
                requested_book.title AS requested_title
            FROM exchange_request er
            INNER JOIN user requester ON requester.id = er.requester_id
            INNER JOIN user requested ON requested.id = er.requested_id
            INNER JOIN book offered ON offered.id = er.offered_book_id
            INNER JOIN book requested_book ON requested_book.id = er.requested_book_id
            WHERE er.requester_id = :userId OR er.requested_id = :userId
            ORDER BY er.created_at DESC
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        $requests = [];

        foreach ($rows as $row) {
            $isRequester = (int) $row['requester_id'] === $userId;
            $requests[] = [
                'type' => $isRequester ? 'outgoing' : 'incoming',
                'status' => $row['status'],
                'requested_book_title' => $row['requested_title'],
                'offered_book_title' => $row['offered_title'],
                'other_user' => $isRequester ? $row['requested_name'] : $row['requester_name'],
                'created_at' => $row['created_at'],
            ];
        }

        return $requests;
    }
}

