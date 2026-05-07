<?php

declare(strict_types=1);

namespace School\Infrastructure\Controller;

use School\Domain\Entity\Course;
use School\Domain\Repository\CourseRepositoryInterface;
use School\Infrastructure\Api\ApiResponse;

class SubjectApiController
{
    public function __construct(
        private readonly CourseRepositoryInterface $courseRepository,
    ) {}

    // GET /api/subjects
    public function index(): void
    {
        $courses = $this->courseRepository->findAll();
        $result  = array_map(fn(Course $c) => $this->format($c), $courses);
        ApiResponse::success($result);
    }

    // GET /api/subjects/{id}
    public function show(int $id): void
    {
        $course = $this->courseRepository->findById($id);
        if ($course === null) {
            ApiResponse::notFound("Subject with id {$id} not found");
            return;
        }
        ApiResponse::success($this->format($course));
    }

    // POST /api/subjects
    public function store(): void
    {
        $body   = $this->getJsonBody();
        $errors = $this->validate($body, ['name', 'code', 'credits']);
        if (!empty($errors)) {
            ApiResponse::error('Validation failed', 422, $errors);
            return;
        }

        $credits = (int)$body['credits'];
        if ($credits <= 0) {
            ApiResponse::error('Validation failed', 422, ['credits' => 'credits must be a positive integer']);
            return;
        }

        $course = new Course(null, trim($body['name']), strtoupper(trim($body['code'])), $credits);
        $this->courseRepository->save($course);

        ApiResponse::created($this->format($course), 'Subject created successfully');
    }

    // PUT /api/subjects/{id}
    public function update(int $id): void
    {
        $course = $this->courseRepository->findById($id);
        if ($course === null) {
            ApiResponse::notFound("Subject with id {$id} not found");
            return;
        }

        $body   = $this->getJsonBody();
        $errors = $this->validate($body, ['name', 'code', 'credits']);
        if (!empty($errors)) {
            ApiResponse::error('Validation failed', 422, $errors);
            return;
        }

        $credits = (int)$body['credits'];
        if ($credits <= 0) {
            ApiResponse::error('Validation failed', 422, ['credits' => 'credits must be a positive integer']);
            return;
        }

        $updated = new Course($course->getId(), trim($body['name']), strtoupper(trim($body['code'])), $credits);
        $this->courseRepository->save($updated);

        ApiResponse::success($this->format($updated), 'Subject updated successfully');
    }

    // DELETE /api/subjects/{id}
    public function destroy(int $id): void
    {
        $course = $this->courseRepository->findById($id);
        if ($course === null) {
            ApiResponse::notFound("Subject with id {$id} not found");
            return;
        }
        $this->courseRepository->delete($id);
        ApiResponse::noContent();
    }

    // ─── private helpers ────────────────────────────────────────────────

    private function format(Course $course): array
    {
        return [
            'id'         => $course->getId(),
            'name'       => $course->getName(),
            'code'       => $course->getCode(),
            'credits'    => $course->getCredits(),
            'created_at' => $course->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }

    private function getJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        return json_decode($raw ?: '{}', true) ?? [];
    }

    private function validate(array $data, array $required): array
    {
        $errors = [];
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                $errors[$field] = "{$field} is required";
            }
        }
        return $errors;
    }
}
