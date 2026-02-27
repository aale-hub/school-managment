<?php

namespace School\Domain\Entity;

use School\Domain\ValueObject\Email;

class User
{
    private ?int $id;
    private string $name;
    private Email $email;
    private \DateTime $createdAt;

    public function __construct(?int $id, string $name, Email $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => (string)$this->email,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }

    public static function fromArray(array $data): self
    {
        $user = new self(
            $data['id'] ?? null,
            $data['name'],
            new Email($data['email']),
                !empty($data['createdAt']) ? new \DateTime($data['createdAt']) : new \DateTime()
        );
        return $user;
    }
}
