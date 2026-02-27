<?php

namespace School\Infrastructure\Persistence\InMemory;

use School\Domain\Entity\Course;
use School\Domain\Repository\CourseRepositoryInterface;

class InMemoryCourseRepository implements CourseRepositoryInterface
{
    private string $filePath;
    /** @var Course[] */
    private array $courses = [];
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

        $this->courses = [];
        $maxId = 0;
        foreach ($data as $item) {
            $course = Course::fromArray($item);
            if ($course->getId() !== null) {
                $this->courses[$course->getId()] = $course;
                $maxId = max($maxId, $course->getId());
            }
        }
        $this->nextId = $maxId + 1;
    }

    private function persistToFile(): void
    {
        $array = [];
        foreach ($this->courses as $course) {
            $array[] = $course->toArray();
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

    public function save(Course $course): void
    {
        if ($course->getId() === null) {
            $course->setId($this->nextId++);
        } else {
            $this->nextId = max($this->nextId, $course->getId() + 1);
        }
        $this->courses[$course->getId()] = $course;
        $this->persistToFile();
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
        if (isset($this->courses[$id])) {
            unset($this->courses[$id]);
            $this->persistToFile();
        }
    }
}
