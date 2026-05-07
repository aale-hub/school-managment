<?php

namespace School\Infrastructure\Persistence\InMemory;

use School\Domain\Entity\Student;
use School\Domain\Repository\StudentRepositoryInterface;

class InMemoryStudentRepository implements StudentRepositoryInterface
{
    private array $students = [];
    private int $nextId = 1;

    public function save(Student $student): void
    {
        if ($student->getId() === null) {
            $student->setId($this->nextId++);
        }
        $this->students[$student->getId()] = $student;
    }

    public function findById(int $id): ?Student
    {
        return $this->students[$id] ?? null;
    }

    public function findByUserId(int $userId): ?Student
    {
        foreach ($this->students as $student) {
            if ($student->getUserId() === $userId) {
                return $student;
            }
        }
        return null;
    }

    public function findByEnrollmentNumber(string $enrollmentNumber): ?Student
    {
        foreach ($this->students as $student) {
            if ($student->getEnrollmentNumber() === $enrollmentNumber) {
                return $student;
            }
        }
        return null;
    }

    public function findAll(): array
    {
        return array_values($this->students);
    }
    public function delete(int $id): void
    {
        unset($this->students[$id]);
    }
}
