<?php

class BookRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * @return Book[]
     */
    public function findLatest(int $limit = 4): array
    {
        $sql = <<<SQL
            SELECT
                b.id AS book_id,
                b.title,
                b.author,
                b.description,
                b.book_condition,
                b.is_available,
                b.cover_image_path,
                b.created_at,
                b.updated_at,
                u.id AS owner_id,
                u.username AS owner_username,
                u.city AS owner_city,
                u.avatar_path AS owner_avatar,
                g.id AS genre_id,
                g.name AS genre_name
            FROM book b
            INNER JOIN user u ON u.id = b.user_id
            LEFT JOIN genre g ON g.id = b.genre_id
            WHERE b.is_available = 1
            ORDER BY b.created_at DESC
            LIMIT :limit
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(fn (array $row) => $this->hydrateBook($row), $stmt->fetchAll());
    }

    /**
     * @return Book[]
     */
    public function findAll(): array
    {
        $sql = <<<SQL
            SELECT
                b.id AS book_id,
                b.title,
                b.author,
                b.description,
                b.book_condition,
                b.is_available,
                b.cover_image_path,
                b.created_at,
                b.updated_at,
                u.id AS owner_id,
                u.username AS owner_username,
                u.city AS owner_city,
                u.avatar_path AS owner_avatar,
                g.id AS genre_id,
                g.name AS genre_name
            FROM book b
            INNER JOIN user u ON u.id = b.user_id
            LEFT JOIN genre g ON g.id = b.genre_id
            ORDER BY b.created_at DESC
        SQL;

        $stmt = $this->pdo->query($sql);

        return array_map(fn (array $row) => $this->hydrateBook($row), $stmt->fetchAll());
    }

    public function find(int $id): ?Book
    {
        $sql = <<<SQL
            SELECT
                b.id AS book_id,
                b.title,
                b.author,
                b.description,
                b.book_condition,
                b.is_available,
                b.cover_image_path,
                b.created_at,
                b.updated_at,
                u.id AS owner_id,
                u.username AS owner_username,
                u.city AS owner_city,
                u.avatar_path AS owner_avatar,
                g.id AS genre_id,
                g.name AS genre_name
            FROM book b
            INNER JOIN user u ON u.id = b.user_id
            LEFT JOIN genre g ON g.id = b.genre_id
            WHERE b.id = :id
            LIMIT 1
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();

        return $row ? $this->hydrateBook($row) : null;
    }

    private function hydrateBook(array $row): Book
    {
        $owner = new User(
            (int) $row['owner_id'],
            $row['owner_username'],
            $row['owner_city'],
            $row['owner_avatar']
        );

        $genre = null;
        if ($row['genre_id'] !== null) {
            $genre = new Genre(
                (int) $row['genre_id'],
                $row['genre_name']
            );
        }

        return new Book(
            (int) $row['book_id'],
            $row['title'],
            $row['author'],
            $row['description'],
            $row['book_condition'],
            (bool) $row['is_available'],
            $row['cover_image_path'],
            new DateTimeImmutable($row['created_at']),
            $row['updated_at'] ? new DateTimeImmutable($row['updated_at']) : null,
            $owner,
            $genre
        );
    }
}
