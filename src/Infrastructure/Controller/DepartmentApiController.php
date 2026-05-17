<?php

declare(strict_types=1);

namespace School\Infrastructure\Controller;

use School\Domain\Entity\Department;
use School\Domain\Repository\DepartmentRepositoryInterface;
use School\Infrastructure\Api\ApiResponse;

class DepartmentApiController
{
    public function __construct(
        private readonly DepartmentRepositoryInterface $departmentRepository,
    ) {
        
    }

    public function index(): void
    {
        $departments = $this->departmentRepository->findAll();
        $result      = array_map(fn(Department $d) => $this->format($d), $departments);
        ApiResponse::success($result);
    }

    public function show(int $id): void
    {
        $department = $this->departmentRepository->findById($id);
        if ($department === null) {
            ApiResponse::notFound("Department with id {$id} not found");
            return;
        }
        ApiResponse::success($this->format($department));
    }

    public function store(): void
    {
        $body   = $this->getJsonBody();
        $errors = $this->validate($body, ['name', 'code']);
        if (!empty($errors)) {
            ApiResponse::error('Validation failed', 422, $errors);
            return;
        }

        if ($this->departmentRepository->findByCode(strtoupper(trim($body['code']))) !== null) {
            ApiResponse::error('Department code already exists', 409);
            return;
        }

        $department = new Department(null, trim($body['name']), strtoupper(trim($body['code'])));
        $this->departmentRepository->save($department);

        ApiResponse::created($this->format($department), 'Department created successfully');
    }

    public function update(int $id): void
    {
        $department = $this->departmentRepository->findById($id);
        if ($department === null) {
            ApiResponse::notFound("Department with id {$id} not found");
            return;
        }

        $body   = $this->getJsonBody();
        $errors = $this->validate($body, ['name', 'code']);
        if (!empty($errors)) {
            ApiResponse::error('Validation failed', 422, $errors);
            return;
        }

        $existing = $this->departmentRepository->findByCode(strtoupper(trim($body['code'])));
        if ($existing !== null && $existing->getId() !== $id) {
            ApiResponse::error('Department code already exists', 409);
            return;
        }

        $updated = new Department($department->getId(), trim($body['name']), strtoupper(trim($body['code'])));
        $this->departmentRepository->save($updated);

        ApiResponse::success($this->format($updated), 'Department updated successfully');
    }

    public function destroy(int $id): void
    {
        $department = $this->departmentRepository->findById($id);
        if ($department === null) {
            ApiResponse::notFound("Department with id {$id} not found");
            return;
        }
        $this->departmentRepository->delete($id);
        ApiResponse::noContent();
    }

    private function format(Department $department): array
    {
        return [
            'id'         => $department->getId(),
            'name'       => $department->getName(),
            'code'       => $department->getCode(),
            'created_at' => $department->getCreatedAt()->format('Y-m-d H:i:s'),
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
