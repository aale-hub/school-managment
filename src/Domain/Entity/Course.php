<?php

namespace School\Domain\Entity;

class Course
{
    private ?int $id;
    private string $name;
    private string $code;
    private int $credits;
    private \DateTime $createdAt;

    public function __construct(?int $id, string $name, string $code, int $credits)
    {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->credits = $credits;
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

    public function getCode(): string
    {
        return $this->code;
    }

    public function getCredits(): int
    {
        return $this->credits;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $dt): void
    {
        $this->createdAt = $dt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'credits' => $this->credits,
            'createdAt' => $this->createdAt->format(\DateTime::ATOM),
        ];
    }

    public static function fromArray(array $data): self
    {
        $course = new self(
            isset($data['id']) ? (int)$data['id'] : null,
            isset($data['name']) ? (string)$data['name'] : '',
            isset($data['code']) ? (string)$data['code'] : '',
            isset($data['credits']) ? (int)$data['credits'] : 0
        );

        if (!empty($data['createdAt'])) {
            try {
                $dt = new \DateTime($data['createdAt']);
                $course->setCreatedAt($dt);
            } catch (\Exception $e) {
                // si falla el parseo dejamos la fecha actual
            }
        }

        return $course;
    }
}
