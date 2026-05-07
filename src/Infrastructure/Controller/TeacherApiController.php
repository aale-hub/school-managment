<?php

declare(strict_types=1);

namespace School\Infrastructure\Controller;

use School\Domain\Entity\Teacher;
use School\Domain\Entity\User;
use School\Domain\Repository\TeacherRepositoryInterface;
use School\Domain\Repository\UserRepositoryInterface;
use School\Infrastructure\Api\ApiResponse;
use School\Domain\Repository\DepartmentRepositoryInterface;
use School\Domain\ValueObject\Email;

class TeacherApiController
{
    public function __construct(
        private readonly TeacherRepositoryInterface    $teacherRepository,
        private readonly UserRepositoryInterface       $userRepository,
        private readonly DepartmentRepositoryInterface $departmentRepository,
    ) {}

    // GET /api/teachers
    public function index(): void
    {
        $teachers = $this->teacherRepository->findAll();
        $result   = array_map(fn(Teacher $t) => $this->format($t), $teachers);
        ApiResponse::success($result);
    }

    // GET /api/teachers/{id}
    public function show(int $id): void
    {
        $teacher = $this->teacherRepository->findById($id);
        if ($teacher === null) {
            ApiResponse::notFound("Teacher with id {$id} not found");
            return;
        }
        ApiResponse::success($this->format($teacher));
    }

    // POST /api/teachers
    public function store(): void
    {
        $body = $this->getJsonBody();

        $errors = $this->validate($body, ['name', 'email', 'specialty']);
        if (!empty($errors)) {
            ApiResponse::error('Validation failed', 422, $errors);
            return;
        }

        try {
            $user = new User(null, trim($body['name']), new Email(trim($body['email'])));
            $this->userRepository->save($user);

            $departmentId = isset($body['department_id']) ? (int)$body['department_id'] : null;

            if ($departmentId !== null && $this->departmentRepository->findById($departmentId) === null) {
                ApiResponse::error("Department with id {$departmentId} not found", 404);
                return;
            }

            $teacher = new Teacher(null, $user->getId(), trim($body['specialty']), $departmentId);
            $this->teacherRepository->save($teacher);

            ApiResponse::created($this->format($teacher), 'Teacher created successfully');
        } catch (\InvalidArgumentException $e) {
            ApiResponse::error($e->getMessage(), 422);
        }
    }

    // PUT /api/teachers/{id}
    public function update(int $id): void
    {
        $teacher = $this->teacherRepository->findById($id);
        if ($teacher === null) {
            ApiResponse::notFound("Teacher with id {$id} not found");
            return;
        }

        $body   = $this->getJsonBody();
        $errors = $this->validate($body, ['name', 'email', 'specialty']);
        if (!empty($errors)) {
            ApiResponse::error('Validation failed', 422, $errors);
            return;
        }

        try {
            $user = $this->userRepository->findById($teacher->getUserId());
            if ($user === null) {
                ApiResponse::serverError('Associated user not found');
                return;
            }
            $user->setName(trim($body['name']));
            $user->setEmail(new Email(trim($body['email'])));
            $this->userRepository->save($user);
            $departmentId = isset($body['department_id']) ? (int)$body['department_id'] : $teacher->getDepartmentId();

            if ($departmentId !== null && $this->departmentRepository->findById($departmentId) === null) {
                ApiResponse::error("Department with id {$departmentId} not found", 404);
                return;
            }

            $updated = new Teacher($teacher->getId(), $user->getId(), trim($body['specialty']), $departmentId);
            $this->teacherRepository->save($updated);

            ApiResponse::success($this->format($updated), 'Teacher updated successfully');
        } catch (\InvalidArgumentException $e) {
            ApiResponse::error($e->getMessage(), 422);
        }
    }

    // DELETE /api/teachers/{id}
    public function destroy(int $id): void
    {
        $teacher = $this->teacherRepository->findById($id);
        if ($teacher === null) {
            ApiResponse::notFound("Teacher with id {$id} not found");
            return;
        }
        $this->teacherRepository->delete($id);
        ApiResponse::noContent();
    }

    // POST /api/teachers/{id}/assign-department
    public function assignDepartment(int $id): void
    {
        $teacher = $this->teacherRepository->findById($id);
        if ($teacher === null) {
            ApiResponse::notFound("Teacher with id {$id} not found");
            return;
        }

        $body         = $this->getJsonBody();
        $departmentId = isset($body['department_id']) ? (int)$body['department_id'] : null;

        if ($departmentId === null) {
            ApiResponse::error('Validation failed', 422, ['department_id' => 'department_id is required']);
            return;
        }

        if ($this->departmentRepository->findById($departmentId) === null) {
            ApiResponse::notFound("Department with id {$departmentId} not found");
            return;
        }

        $teacher->assignToDepartment($departmentId);
        $this->teacherRepository->save($teacher);

        ApiResponse::success($this->format($teacher), 'Department assigned successfully');
    }

    // ─── private helpers ────────────────────────────────────────────────

    private function format(Teacher $teacher): array
    {
        $user = $this->userRepository->findById($teacher->getUserId());
        return [
            'id'            => $teacher->getId(),
            'name'          => $user?->getName(),
            'email'         => $user?->getEmail()->getValue(),
            'specialty'     => $teacher->getSpecialty(),
            'department_id' => $teacher->getDepartmentId(),
            'hired_at'      => $teacher->getHiredAt()->format('Y-m-d H:i:s'),
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
