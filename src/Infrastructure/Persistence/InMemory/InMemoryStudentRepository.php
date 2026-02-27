<?php
namespace School\Infrastructure\Persistence\InMemory;

use School\Domain\Entity\Student;
use School\Domain\Repository\StudentRepositoryInterface;

class InMemoryStudentRepository implements StudentRepositoryInterface
{
    private string $filePath;
    /** @var Student[] */
    private array $students = [];
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

        $this->students = [];
        $maxId = 0;
        foreach ($data as $item) {
            $student = Student::fromArray($item);
            if ($student->getId() !== null) {
                $this->students[$student->getId()] = $student;
                $maxId = max($maxId, $student->getId());
            }
        }
        $this->nextId = $maxId + 1;
    }

    private function persistToFile(): void
    {
        $array = [];
        foreach ($this->students as $student) {
            $array[] = $student->toArray();
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

    public function save(Student $student): void
    {
        if ($student->getId() === null) {
            $student->setId($this->nextId++);
        } else {
            $this->nextId = max($this->nextId, $student->getId() + 1);
        }
        $this->students[$student->getId()] = $student;
        $this->persistToFile();
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
        if (isset($this->students[$id])) {
            unset($this->students[$id]);
            $this->persistToFile();
        }
    }
}
