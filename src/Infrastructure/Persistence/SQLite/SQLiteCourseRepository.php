<?php

declare(strict_types=1);

namespace School\Infrastructure\Persistence\SQLite;

use School\Domain\Entity\Course;
use School\Domain\Repository\CourseRepositoryInterface;

class SQLiteCourseRepository implements CourseRepositoryInterface
{
    public function save(Course $course): void
    {
        $pdo = Connection::get();

        if ($course->getId() === null) {
            $stmt = $pdo->prepare(
                'INSERT INTO courses (name, code, credits, created_at)
                 VALUES (:name, :code, :credits, :created_at)'
            );
            $stmt->execute([
                'name'       => $course->getName(),
                'code'       => $course->getCode(),
                'credits'    => $course->getCredits(),
                'created_at' => $course->getCreatedAt()->format('Y-m-d H:i:s'),
            ]);
            $course->setId((int) $pdo->lastInsertId());
        } else {
            $stmt = $pdo->prepare(
                'UPDATE courses SET name = :name, code = :code, credits = :credits WHERE id = :id'
            );
            $stmt->execute([
                'name'    => $course->getName(),
                'code'    => $course->getCode(),
                'credits' => $course->getCredits(),
                'id'      => $course->getId(),
            ]);
        }
    }

    public function findById(int $id): ?Course
    {
        $stmt = Connection::get()->prepare('SELECT * FROM courses WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findByCode(string $code): ?Course
    {
        $stmt = Connection::get()->prepare('SELECT * FROM courses WHERE code = :code');
        $stmt->execute(['code' => $code]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findAll(): array
    {
        $rows = Connection::get()->query('SELECT * FROM courses')->fetchAll();
        return array_map([$this, 'hydrate'], $rows);
    }

    public function delete(int $id): void
    {
        $stmt = Connection::get()->prepare('DELETE FROM courses WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    private function hydrate(array $row): Course
    {
        $course = new Course(
            (int) $row['id'],
            $row['name'],
            $row['code'],
            (int) $row['credits'],
        );
        return $course;
    }
}
