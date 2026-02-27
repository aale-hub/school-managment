<?php

namespace School\Domain\Entity;

class Student 
{
    private ?int $id;
    private int $userId;
    private string $enrollmentNumber;
    private ?int $courseId;
    private \DateTime $enrolledAt;

    public function __construct(?int $id, int $userId, string $enrollmentNumber, ?int $courseId = null)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->enrollmentNumber = $enrollmentNumber;
        $this->courseId = $courseId;
        $this->enrolledAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getEnrollmentNumber(): string
    {
        return $this->enrollmentNumber;
    }

    public function getCourseId(): ?int
    {
        return $this->courseId;
    }

    public function assignToCourse(int $courseId): void
    {
        $this->courseId = $courseId;
    }

    public function getEnrolledAt(): \DateTime
    {
        return $this->enrolledAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'enrollmentNumber' => $this->enrollmentNumber,
            'courseId' => $this->courseId,
            'enrolledAt' => $this->enrolledAt->format(\DateTime::ATOM),
        ];
    }

    public static function fromArray(array $data): self
    {
        $student = new self(
            isset($data['id']) ? (int)$data['id'] : null,
            isset($data['userId']) ? (int)$data['userId'] : 0,
            isset($data['enrollmentNumber']) ? (string)$data['enrollmentNumber'] : '',
            $data['courseId'] ?? null
        );

        return $student;
    }
}
