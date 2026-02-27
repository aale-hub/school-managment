<?php

namespace School\Infrastructure\Controller;

use School\Domain\Repository\StudentRepositoryInterface;
use School\Domain\Repository\TeacherRepositoryInterface;
use School\Domain\Repository\DepartmentRepositoryInterface;
use School\Domain\Repository\CourseRepositoryInterface;
use School\Domain\Repository\UserRepositoryInterface;

class HomeController
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

    public function student(): void
    {
        $courses = $this->courseRepository->findAll();
        $users = $this->userRepository->findAll();
        $students = $this->studentRepository->findAll();
        require __DIR__ . '/../View/student.php';
    }

    public function teacher(): void
    {
        $departments = $this->departmentRepository->findAll();
        $users = $this->userRepository->findAll();
        $teachers = $this->teacherRepository->findAll();
        require __DIR__ . '/../View/teacher.php';
    }
    public function department(): void
    {
        $departments = $this->departmentRepository->findAll();
        require __DIR__ . '/../View/department.php';
    }
    public function course(): void
    {
        $courses = $this->courseRepository->findAll();
        require __DIR__ . '/../View/course.php';
    }
    public function home(): void
    {
        $users = $this->userRepository->findAll();
        $students = $this->studentRepository->findAll();
        require __DIR__ . '/../View/home.php';
    }
}
