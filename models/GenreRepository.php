<?php

class GenreRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * @return Genre[]
     */
    public function findAll(): array
    {
        $sql = 'SELECT id, name FROM genre ORDER BY name ASC';
        $stmt = $this->pdo->query($sql);

        return array_map(
            fn (array $row) => new Genre((int) $row['id'], $row['name']),
            $stmt->fetchAll()
        );
    }
}
