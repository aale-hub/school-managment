<?php

declare(strict_types=1);

namespace School\Infrastructure\Persistence\SQLite;

use School\Domain\Entity\Teacher;
use School\Domain\Repository\TeacherRepositoryInterface;

class SQLiteTeacherRepository implements TeacherRepositoryInterface
{
    public function save(Teacher $teacher): void
    {
        $pdo = Connection::get();

        if ($teacher->getId() === null) {
            $stmt = $pdo->prepare(
                'INSERT INTO teachers (user_id, specialty, department_id, hired_at)
                 VALUES (:user_id, :specialty, :department_id, :hired_at)'
            );
            $stmt->execute([
                'user_id'       => $teacher->getUserId(),
                'specialty'     => $teacher->getSpecialty(),
                'department_id' => $teacher->getDepartmentId(),
                'hired_at'      => $teacher->getHiredAt()->format('Y-m-d H:i:s'),
            ]);
            $teacher->setId((int) $pdo->lastInsertId());
        } else {
            $stmt = $pdo->prepare(
                'UPDATE teachers SET user_id = :user_id, specialty = :specialty,
                 department_id = :department_id WHERE id = :id'
            );
            $stmt->execute([
                'user_id'       => $teacher->getUserId(),
                'specialty'     => $teacher->getSpecialty(),
                'department_id' => $teacher->getDepartmentId(),
                'id'            => $teacher->getId(),
            ]);
        }
    }

    public function findById(int $id): ?Teacher
    {
        $stmt = Connection::get()->prepare('SELECT * FROM teachers WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findByUserId(int $userId): ?Teacher
    {
        $stmt = Connection::get()->prepare('SELECT * FROM teachers WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findAll(): array
    {
        $rows = Connection::get()->query('SELECT * FROM teachers')->fetchAll();
        return array_map([$this, 'hydrate'], $rows);
    }

    public function delete(int $id): void
    {
        $stmt = Connection::get()->prepare('DELETE FROM teachers WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    private function hydrate(array $row): Teacher
    {
        $teacher = new Teacher(
            (int) $row['id'],
            (int) $row['user_id'],
            $row['specialty'],
            $row['department_id'] !== null ? (int) $row['department_id'] : null,
        );
        return $teacher;
    }
}
