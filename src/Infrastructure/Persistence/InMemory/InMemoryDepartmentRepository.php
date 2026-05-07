<?php

namespace School\Infrastructure\Persistence\InMemory;

use School\Domain\Entity\Department;
use School\Domain\Repository\DepartmentRepositoryInterface;

class InMemoryDepartmentRepository implements DepartmentRepositoryInterface
{
    private array $departments = [];
    private int $nextId = 1;

    public function save(Department $department): void
    {
        if ($department->getId() === null) {
            $department->setId($this->nextId++);
        }
        $this->departments[$department->getId()] = $department;
    }

    public function findById(int $id): ?Department
    {
        return $this->departments[$id] ?? null;
    }

    public function findByCode(string $code): ?Department
    {
        foreach ($this->departments as $department) {
            if ($department->getCode() === $code) {
                return $department;
            }
        }
        return null;
    }

    public function findAll(): array
    {
        return array_values($this->departments);
    }

    public function delete(int $id): void
    {
        unset($this->departments[$id]);
    }
}
