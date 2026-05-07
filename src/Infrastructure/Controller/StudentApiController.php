<?php

declare(strict_types=1);

namespace School\Infrastructure\Controller;

use School\Domain\Entity\Student;
use School\Domain\Entity\User;
use School\Domain\Repository\StudentRepositoryInterface;
use School\Domain\Repository\UserRepositoryInterface;
use School\Domain\Repository\CourseRepositoryInterface;
use School\Domain\ValueObject\Email;
use School\Infrastructure\Api\ApiResponse;
class StudentApiController
{
    public function __construct(
        private readonly StudentRepositoryInterface $studentRepository,
        private readonly UserRepositoryInterface    $userRepository,
        private readonly CourseRepositoryInterface  $courseRepository,
    ) {}

    // GET /api/students
    public function index(): void
    {
        $students = $this->studentRepository->findAll();
        $result   = array_map(fn(Student $s) => $this->format($s), $students);
        ApiResponse::success($result);
    }

    // GET /api/students/{id}
    public function show(int $id): void
    {
        $student = $this->studentRepository->findById($id);
        if ($student === null) {
            ApiResponse::notFound("Student with id {$id} not found");
            return;
        }
        ApiResponse::success($this->format($student));
    }

    // POST /api/students
    public function store(): void
    {
        $body   = $this->getJsonBody();
        $errors = $this->validate($body, ['name', 'email', 'enrollment_number']);
        if (!empty($errors)) {
            ApiResponse::error('Validation failed', 422, $errors);
            return;
        }

        // Check unique enrollment number
        if ($this->studentRepository->findByEnrollmentNumber(trim($body['enrollment_number'])) !== null) {
            ApiResponse::error('Enrollment number already exists', 409);
            return;
        }

        try {
            $user = new User(null, trim($body['name']), new Email(trim($body['email'])));
            $this->userRepository->save($user);

            $courseId = isset($body['course_id']) ? (int)$body['course_id'] : null;

            if ($courseId !== null && $this->courseRepository->findById($courseId) === null) {
                ApiResponse::error("Course with id {$courseId} not found", 404);
                return;
            }

            $student = new Student(null, $user->getId(), trim($body['enrollment_number']), $courseId);
            $this->studentRepository->save($student);

            ApiResponse::created($this->format($student), 'Student created successfully');
        } catch (\InvalidArgumentException $e) {
            ApiResponse::error($e->getMessage(), 422);
        }
    }

    // PUT /api/students/{id}
    public function update(int $id): void
    {
        $student = $this->studentRepository->findById($id);
        if ($student === null) {
            ApiResponse::notFound("Student with id {$id} not found");
            return;
        }

        $body   = $this->getJsonBody();
        $errors = $this->validate($body, ['name', 'email', 'enrollment_number']);
        if (!empty($errors)) {
            ApiResponse::error('Validation failed', 422, $errors);
            return;
        }

        try {
            $user = $this->userRepository->findById($student->getUserId());
            if ($user === null) {
                ApiResponse::serverError('Associated user not found');
                return;
            }
            $user->setName(trim($body['name']));
            $user->setEmail(new Email(trim($body['email'])));
            $this->userRepository->save($user);

            $courseId = isset($body['course_id']) ? (int)$body['course_id'] : $student->getCourseId();

            if ($courseId !== null && $this->courseRepository->findById($courseId) === null) {
                ApiResponse::error("Course with id {$courseId} not found", 404);
                return;
            }

            $updated = new Student($student->getId(), $user->getId(), trim($body['enrollment_number']), $courseId);
            $this->studentRepository->save($updated);

            ApiResponse::success($this->format($updated), 'Student updated successfully');
        } catch (\InvalidArgumentException $e) {
            ApiResponse::error($e->getMessage(), 422);
        }
    }

    // DELETE /api/students/{id}
    public function destroy(int $id): void
    {
        $student = $this->studentRepository->findById($id);
        if ($student === null) {
            ApiResponse::notFound("Student with id {$id} not found");
            return;
        }
        $this->studentRepository->delete($id);
        ApiResponse::noContent();
    }

    // POST /api/students/{id}/assign-course
    public function assignCourse(int $id): void
    {
        $student = $this->studentRepository->findById($id);
        if ($student === null) {
            ApiResponse::notFound("Student with id {$id} not found");
            return;
        }

        $body     = $this->getJsonBody();
        $courseId = isset($body['course_id']) ? (int)$body['course_id'] : null;

        if ($courseId === null) {
            ApiResponse::error('Validation failed', 422, ['course_id' => 'course_id is required']);
            return;
        }

        if ($this->courseRepository->findById($courseId) === null) {
            ApiResponse::notFound("Course with id {$courseId} not found");
            return;
        }

        $student->assignToCourse($courseId);
        $this->studentRepository->save($student);

        ApiResponse::success($this->format($student), 'Course assigned successfully');
    }

    // ─── private helpers ────────────────────────────────────────────────

    private function format(Student $student): array
    {
        $user = $this->userRepository->findById($student->getUserId());
        return [
            'id'                => $student->getId(),
            'name'              => $user?->getName(),
            'email'             => $user?->getEmail()->getValue(),
            'enrollment_number' => $student->getEnrollmentNumber(),
            'course_id'         => $student->getCourseId(),
            'enrolled_at'       => $student->getEnrolledAt()->format('Y-m-d H:i:s'),
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
            if (empty($data[$field])) {
                $errors[$field] = "{$field} is required";
            }
        }
        return $errors;
    }
}
