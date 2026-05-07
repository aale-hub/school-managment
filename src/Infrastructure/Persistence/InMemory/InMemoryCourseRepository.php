<?php

namespace School\Infrastructure\Persistence\InMemory;

use School\Domain\Entity\Course;
use School\Domain\Repository\CourseRepositoryInterface;

class InMemoryCourseRepository implements CourseRepositoryInterface
{
    private array $courses = [];
    private int $nextId = 1;

    public function save(Course $course): void
    {
        if ($course->getId() === null) {
            $course->setId($this->nextId++);
        }
        $this->courses[$course->getId()] = $course;
    }

    public function findById(int $id): ?Course
    {
        return $this->courses[$id] ?? null;
    }

    public function findByCode(string $code): ?Course
    {
        foreach ($this->courses as $course) {
            if ($course->getCode() === $code) {
                return $course;
            }
        }
        return null;
    }

    public function findAll(): array
    {
        return array_values($this->courses);
    }
    public function delete(int $id): void
    {
        unset($this->courses[$id]);
    }
}
