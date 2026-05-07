<?php

declare(strict_types=1);

namespace School\Infrastructure\Persistence\SQLite;

use School\Domain\Entity\Student;
use School\Domain\Repository\StudentRepositoryInterface;

class SQLiteStudentRepository implements StudentRepositoryInterface
{
    public function save(Student $student): void
    {
        $pdo = Connection::get();

        if ($student->getId() === null) {
            $stmt = $pdo->prepare(
                'INSERT INTO students (user_id, enrollment_number, course_id, enrolled_at)
                 VALUES (:user_id, :enrollment_number, :course_id, :enrolled_at)'
            );
            $stmt->execute([
                'user_id'           => $student->getUserId(),
                'enrollment_number' => $student->getEnrollmentNumber(),
                'course_id'         => $student->getCourseId(),
                'enrolled_at'       => $student->getEnrolledAt()->format('Y-m-d H:i:s'),
            ]);
            $student->setId((int) $pdo->lastInsertId());
        } else {
            $stmt = $pdo->prepare(
                'UPDATE students SET user_id = :user_id, enrollment_number = :enrollment_number,
                 course_id = :course_id WHERE id = :id'
            );
            $stmt->execute([
                'user_id'           => $student->getUserId(),
                'enrollment_number' => $student->getEnrollmentNumber(),
                'course_id'         => $student->getCourseId(),
                'id'                => $student->getId(),
            ]);
        }
    }

    public function findById(int $id): ?Student
    {
        $stmt = Connection::get()->prepare('SELECT * FROM students WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findByUserId(int $userId): ?Student
    {
        $stmt = Connection::get()->prepare('SELECT * FROM students WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findByEnrollmentNumber(string $enrollmentNumber): ?Student
    {
        $stmt = Connection::get()->prepare('SELECT * FROM students WHERE enrollment_number = :en');
        $stmt->execute(['en' => $enrollmentNumber]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findAll(): array
    {
        $rows = Connection::get()->query('SELECT * FROM students')->fetchAll();
        return array_map([$this, 'hydrate'], $rows);
    }

    public function delete(int $id): void
    {
        $stmt = Connection::get()->prepare('DELETE FROM students WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    private function hydrate(array $row): Student
    {
        $student = new Student(
            (int) $row['id'],
            (int) $row['user_id'],
            $row['enrollment_number'],
            $row['course_id'] !== null ? (int) $row['course_id'] : null,
        );
        return $student;
    }
}
