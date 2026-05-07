<?php

declare(strict_types=1);

namespace School\Infrastructure\Persistence\SQLite;

use School\Domain\Entity\User;
use School\Domain\Repository\UserRepositoryInterface;
use School\Domain\ValueObject\Email;

class SQLiteUserRepository implements UserRepositoryInterface
{
    public function save(User $user): void
    {
        $pdo = Connection::get();

        if ($user->getId() === null) {
            $stmt = $pdo->prepare(
                'INSERT INTO users (name, email, created_at) VALUES (:name, :email, :created_at)'
            );
            $stmt->execute([
                'name'       => $user->getName(),
                'email'      => $user->getEmail()->getValue(),
                'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            ]);
            $user->setId((int) $pdo->lastInsertId());
        } else {
            $stmt = $pdo->prepare(
                'UPDATE users SET name = :name, email = :email WHERE id = :id'
            );
            $stmt->execute([
                'name'  => $user->getName(),
                'email' => $user->getEmail()->getValue(),
                'id'    => $user->getId(),
            ]);
        }
    }

    public function findById(int $id): ?User
    {
        $stmt = Connection::get()->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findByEmail(Email $email): ?User
    {
        $stmt = Connection::get()->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email->getValue()]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findAll(): array
    {
        $rows = Connection::get()->query('SELECT * FROM users')->fetchAll();
        return array_map([$this, 'hydrate'], $rows);
    }

    public function delete(int $id): void
    {
        $stmt = Connection::get()->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    private function hydrate(array $row): User
    {
        $user = new User((int) $row['id'], $row['name'], new Email($row['email']));
        return $user;
    }
}
