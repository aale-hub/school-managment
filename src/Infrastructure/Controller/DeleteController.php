<?php
declare(strict_types=1);

namespace School\Infrastructure\Controller;

use School\Domain\Repository\StudentRepositoryInterface;
use School\Domain\Repository\TeacherRepositoryInterface;
use School\Domain\Repository\DepartmentRepositoryInterface;
use School\Domain\Repository\CourseRepositoryInterface;
use School\Domain\Repository\UserRepositoryInterface;
use School\Domain\ValueObject\Email;

class DeleteController
{
    private StudentRepositoryInterface $studentRepository;
    private TeacherRepositoryInterface $teacherRepository;
    private DepartmentRepositoryInterface $departmentRepository;
    private CourseRepositoryInterface $courseRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        StudentRepositoryInterface $studentRepository,
        TeacherRepositoryInterface $teacherRepository,
        DepartmentRepositoryInterface $departmentRepository,
        CourseRepositoryInterface $courseRepository,
        UserRepositoryInterface $userRepository
        
    ) {
        $this->studentRepository = $studentRepository;
        $this->teacherRepository = $teacherRepository;
        $this->departmentRepository = $departmentRepository;
        $this->courseRepository = $courseRepository;
        $this->userRepository = $userRepository;
    }

    public function deleteStudent(): void
    {
        $Id = (int)$_GET['id'];
        $student = $this->studentRepository->findById($Id);
        if ($student) {
            $this->studentRepository->delete($Id);
            $this->userRepository->delete($student->getUserId());
        }
        $users = $this->userRepository->findAll();
        $students = $this->studentRepository->findAll();
        header('Location: /student');
        exit();
    }

    public function deleteTeacher(): void
    {
        $Id = (int)$_GET['id'];
        $teacher = $this->teacherRepository->findById($Id);
        if ($teacher) {
            $this->teacherRepository->delete($Id);
            $this->userRepository->delete($teacher->getUserId());
        }
        $users = $this->userRepository->findAll();
        $teachers = $this->teacherRepository->findAll();
        header('Location: /teacher');
        exit();
    }

    public function deleteDepartment(): void
    {
        $Id = (int)$_GET['id'];
        $department = $this->departmentRepository->findById($Id);
        if ($department) {
            $this->departmentRepository->delete($Id);
        }
        $departments = $this->departmentRepository->findAll();
        header('Location: /department');
        exit();
    }

    public function deleteCourse(): void
    {
        $Id = (int)$_GET['id'];
        $course = $this->courseRepository->findById($Id);
        if ($course) {
            $this->courseRepository->delete($Id);
        }
        $courses = $this->courseRepository->findAll();
        header('Location: /course');
        exit();
    }
}