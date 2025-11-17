<?php

class UserRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    public function create(string $username, string $email, string $plainPassword): User
    {
        $hash = password_hash($plainPassword, PASSWORD_BCRYPT);

        $sql = 'INSERT INTO user (username, email, password_hash) VALUES (:username, :email, :password_hash)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password_hash' => $hash,
        ]);

        return $this->find((int) $this->pdo->lastInsertId());
    }

    public function find(int $id): ?User
    {
        return $this->findByColumn('id', $id);
    }

    public function findOneByEmail(string $email): ?User
    {
        return $this->findByColumn('email', $email);
    }

    public function findOneByUsername(string $username): ?User
    {
        return $this->findByColumn('username', $username);
    }

    public function findOneByLogin(string $login): ?User
    {
        $sql = <<<SQL
            SELECT
                id,
                username,
                email,
                password_hash,
                city,
                bio,
                avatar_path,
                roles,
                is_active
            FROM user
            WHERE email = :login OR username = :login
            LIMIT 1
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':login', $login);
        $stmt->execute();

        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    private function findByColumn(string $column, string|int $value): ?User
    {
        $allowedColumns = ['id', 'email', 'username'];
        if (!in_array($column, $allowedColumns, true)) {
            throw new InvalidArgumentException('Colonne non autorisée pour la recherche utilisateur.');
        }

        $sql = <<<SQL
            SELECT
                id,
                username,
                email,
                password_hash,
                city,
                bio,
                avatar_path,
                roles,
                is_active
            FROM user
            WHERE {$column} = :value
            LIMIT 1
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':value', $value);
        $stmt->execute();

        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    private function hydrate(array $row): User
    {
        return new User(
            (int) $row['id'],
            $row['username'],
            $row['city'] ?? null,
            $row['avatar_path'] ?? null,
            $row['email'] ?? null,
            $row['password_hash'] ?? null,
            $row['bio'] ?? null,
            $row['roles'] ?? null,
            isset($row['is_active']) ? (bool) $row['is_active'] : null
        );
    }
}
