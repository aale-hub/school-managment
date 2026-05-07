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
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setEmail(Email $email): void
    {
        $this->email = $email;
    }
}
