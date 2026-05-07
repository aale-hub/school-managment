<?php

namespace School\Infrastructure\Persistence\InMemory;

use School\Domain\Entity\Teacher;
use School\Domain\Repository\TeacherRepositoryInterface;

class InMemoryTeacherRepository implements TeacherRepositoryInterface
{
    private array $teachers = [];
    private int $nextId = 1;

    public function save(Teacher $teacher): void
    {
        if ($teacher->getId() === null) {
            $teacher->setId($this->nextId++);
        }
        $this->teachers[$teacher->getId()] = $teacher;
    }

    public function findById(int $id): ?Teacher
    {
        return $this->teachers[$id] ?? null;
    }

    public function findByUserId(int $userId): ?Teacher
    {
        foreach ($this->teachers as $teacher) {
            if ($teacher->getUserId() === $userId) {
                return $teacher;
            }
        }
        return null;
    }

    public function findAll(): array
    {
        return array_values($this->teachers);
    }
    public function delete(int $id): void
    {
        unset($this->teachers[$id]);
    }
}
