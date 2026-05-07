<?php

declare(strict_types=1);

namespace School\Infrastructure\Persistence\SQLite;

use School\Domain\Entity\Department;
use School\Domain\Repository\DepartmentRepositoryInterface;

class SQLiteDepartmentRepository implements DepartmentRepositoryInterface
{
    public function save(Department $department): void
    {
        $pdo = Connection::get();

        if ($department->getId() === null) {
            $stmt = $pdo->prepare(
                'INSERT INTO departments (name, code, created_at)
                 VALUES (:name, :code, :created_at)'
            );
            $stmt->execute([
                'name'       => $department->getName(),
                'code'       => $department->getCode(),
                'created_at' => $department->getCreatedAt()->format('Y-m-d H:i:s'),
            ]);
            $department->setId((int) $pdo->lastInsertId());
        } else {
            $stmt = $pdo->prepare(
                'UPDATE departments SET name = :name, code = :code WHERE id = :id'
            );
            $stmt->execute([
                'name' => $department->getName(),
                'code' => $department->getCode(),
                'id'   => $department->getId(),
            ]);
        }
    }

    public function findById(int $id): ?Department
    {
        $stmt = Connection::get()->prepare('SELECT * FROM departments WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findByCode(string $code): ?Department
    {
        $stmt = Connection::get()->prepare('SELECT * FROM departments WHERE code = :code');
        $stmt->execute(['code' => $code]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findAll(): array
    {
        $rows = Connection::get()->query('SELECT * FROM departments')->fetchAll();
        return array_map([$this, 'hydrate'], $rows);
    }

    public function delete(int $id): void
    {
        $stmt = Connection::get()->prepare('DELETE FROM departments WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    private function hydrate(array $row): Department
    {
        $dept = new Department(
            (int) $row['id'],
            $row['name'],
            $row['code'],
        );
        return $dept;
    }
}
