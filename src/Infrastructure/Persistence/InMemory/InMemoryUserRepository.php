<?php

namespace School\Infrastructure\Persistence\InMemory;

use School\Domain\Entity\User;
use School\Domain\Repository\UserRepositoryInterface;

class InMemoryUserRepository implements UserRepositoryInterface
{
    private string $filePath;
    /** @var User[] */
    private array $users = [];
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

        $this->users = [];
        $maxId = 0;
        foreach ($data as $item) {
            $user = User::fromArray($item);
            if ($user->getId() !== null) {
                $this->users[$user->getId()] = $user;
                $maxId = max($maxId, $user->getId());
            }
        }
        $this->nextId = $maxId + 1;
    }

    private function persistToFile(): void
    {
        $array = [];
        foreach ($this->users as $user) {
            $array[] = $user->toArray();
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

    public function save(User $user): void
    {
        if ($user->getId() === null) {
            $user->setId($this->nextId++);
        } else {
            $this->nextId = max($this->nextId, $user->getId() + 1);
        }
        $this->users[$user->getId()] = $user;
        $this->persistToFile();
    }

    public function findById(int $id): ?User
    {
        return $this->users[$id] ?? null;
    }

    public function findByEmail(\School\Domain\ValueObject\Email $email): ?User
    {
        foreach ($this->users as $user) {
            if ($user->getEmail()->getValue() === $email->getValue()) {
                return $user;
            }
        }
        return null;
    }

    public function findAll(): array
    {
        return array_values($this->users);
    }

    public function delete(int $id): void
    {
        if (isset($this->users[$id])) {
            unset($this->users[$id]);
            $this->persistToFile();
        }
    }
}
