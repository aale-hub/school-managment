<?php

namespace School\Infrastructure\Persistence\InMemory;

use School\Domain\Entity\Department;
use School\Domain\Repository\DepartmentRepositoryInterface;

class InMemoryDepartmentRepository implements DepartmentRepositoryInterface
{
    private string $filePath;
    /** @var Department[] */
    private array $departments = [];
    private int $nextId = 1;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->loadFromFile();
    }

    private function loadFromFile(): void
    {
        if (!file_exists($this->filePath)) {
            $dir = dirname($this->filePath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            file_put_contents($this->filePath, json_encode([]));
        }

        $content = @file_get_contents($this->filePath);
        $data = [];
        if ($content !== false && $content !== '') {
            $decoded = json_decode($content, true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        $this->departments = [];
        $maxId = 0;
        foreach ($data as $item) {
            $department = Department::fromArray($item);
            if ($department->getId() !== null) {
                $this->departments[$department->getId()] = $department;
                $maxId = max($maxId, $department->getId());
            }
        }
        $this->nextId = $maxId + 1;
    }

    private function persistToFile(): void
    {
        $array = [];
        foreach ($this->departments as $department) {
            $array[] = $department->toArray();
        }
        $json = json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $tmpFile = $this->filePath . '.tmp';
        $fp = fopen($tmpFile, 'c');
        if ($fp === false) {
            throw new \RuntimeException('No se pudo abrir archivo temporal para escritura');
        }

        if (!flock($fp, LOCK_EX)) {
            fclose($fp);
            throw new \RuntimeException('No se pudo bloquear archivo temporal');
        }

        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, $json);
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

        rename($tmpFile, $this->filePath);
    }

    public function save(Department $department): void
    {
        if ($department->getId() === null) {
            $department->setId($this->nextId++);
        } else {
            $this->nextId = max($this->nextId, $department->getId() + 1);
        }
        $this->departments[$department->getId()] = $department;
        $this->persistToFile();
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
        if (isset($this->departments[$id])) {
            unset($this->departments[$id]);
            $this->persistToFile();
        }
    }
}
