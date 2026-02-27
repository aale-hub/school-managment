<?php

namespace School\Infrastructure\Persistence\InMemory;

use School\Domain\Entity\Teacher;
use School\Domain\Repository\TeacherRepositoryInterface;

class InMemoryTeacherRepository implements TeacherRepositoryInterface
{
    private string $filePath;
    /** @var Teacher[] */
    private array $teachers = [];
    private int $nextId = 1;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->loadFromFile();
    }

    public function loadFromFile(): void
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
        $this->teachers = [];
        $maxId = 0;
        foreach ($data as $item) {
            $teacher = Teacher::fromArray($item);
            if ($teacher->getId() !== null) {
                $this->teachers[$teacher->getId()] = $teacher;
                $maxId = max($maxId, $teacher->getId());
            }
        }
        $this->nextId = $maxId + 1;
    }

    private function persistToFile(): void
    {
        $array = [];
        foreach ($this->teachers as $teacher) {
            $array[] = $teacher->toArray();
        }
        $json = json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $tmpFile = $this->filePath . '.tmp';
        $fp = fopen($tmpFile, 'c');
        if ($fp === false) {
            throw new \RuntimeException("No se pudo abrir el archivo temporal para escritura: {$tmpFile}");
        }
        try {
            if (flock($fp, LOCK_EX)) {
                ftruncate($fp, 0);
                fwrite($fp, $json);
                fflush($fp);
                flock($fp, LOCK_UN);
            } else {
                throw new \RuntimeException("No se pudo obtener el bloqueo exclusivo para el archivo temporal: {$tmpFile}");
            }
        } finally {
            fclose($fp);
        }
        rename($tmpFile, $this->filePath);
    }

    public function save(Teacher $teacher): void
    {
        if ($teacher->getId() === null) {
            $teacher->setId($this->nextId++);
        }
        $this->teachers[$teacher->getId()] = $teacher;
        $this->persistToFile();
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
        $this->persistToFile();
    }
}
