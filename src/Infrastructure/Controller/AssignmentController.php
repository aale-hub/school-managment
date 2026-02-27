<?php

namespace School\Infrastructure\Controller;

use School\Application\Service\AssignTeacherDepartmentService;
use School\Application\Service\AssignStudentCourseService;
use School\Domain\Repository\UserRepositoryInterface;
use School\Domain\Repository\TeacherRepositoryInterface;
use School\Domain\Repository\StudentRepositoryInterface;
use School\Domain\Repository\DepartmentRepositoryInterface;
use School\Domain\Repository\CourseRepositoryInterface;

class AssignmentController
{
    private UserRepositoryInterface $userRepository;
    private TeacherRepositoryInterface $teacherRepository;
    private StudentRepositoryInterface $studentRepository;
    private DepartmentRepositoryInterface $departmentRepository;
    private CourseRepositoryInterface $courseRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        TeacherRepositoryInterface $teacherRepository,
        StudentRepositoryInterface $studentRepository,
        DepartmentRepositoryInterface $departmentRepository,
        CourseRepositoryInterface $courseRepository
    ) {
        $this->userRepository = $userRepository;
        $this->teacherRepository = $teacherRepository;
        $this->studentRepository = $studentRepository;
        $this->departmentRepository = $departmentRepository;
        $this->courseRepository = $courseRepository;
    }

    public function showAssignTeacherForm(): void
    {
        $teachers = $this->teacherRepository->findAll();
        $departments = $this->departmentRepository->findAll();
        $users = $this->userRepository->findAll();

        $userNamesById = [];
        foreach ($users as $user) {
            $id = $user->getId();
            if ($id !== null) {
                $userNamesById[$id] = $user->getName();
            }
        }

        require __DIR__ . '/../View/assign_teacher_form.php';
    }


    public function assignTeacher(): void
    {
        $teacherId = (int)$_POST['teacher_id'];
        $departmentId = (int)$_POST['department_id'];

        $service = new AssignTeacherDepartmentService(
            $this->teacherRepository,
            $this->departmentRepository
        );

        try {
            $service->execute($teacherId, $departmentId);
            $message = "Teacher assigned to department successfully!";
        } catch (\Exception $e) {
            $message = "Error: " . $e->getMessage();
        }
        $users = $this->userRepository->findAll();
        $teachers = $this->teacherRepository->findAll();
        $departments = $this->departmentRepository->findAll();
        require __DIR__ . '/../View/teacher.php';
    }

    public function showAssignStudentForm(): void
    {
        $students = $this->studentRepository->findAll();
        $courses = $this->courseRepository->findAll();
        $users = $this->userRepository->findAll();

        $userNamesById = [];
        foreach ($users as $user) {
            $id = $user->getId();
            if ($id !== null) {
                $userNamesById[$id] = $user->getName();
            }
        }

        require __DIR__ . '/../View/assign_student_form.php';
    }


    public function assignStudent(): void
    {
        $studentId = (int)$_POST['student_id'];
        $courseId = (int)$_POST['course_id'];

        $service = new AssignStudentCourseService(
            $this->studentRepository,
            $this->courseRepository
        );

        try {
            $service->execute($studentId, $courseId);
            $message = "Student assigned to course successfully!";
        } catch (\Exception $e) {
            $message = "Error: " . $e->getMessage();
        }
    $users = $this->userRepository->findAll();
        $students = $this->studentRepository->findAll();
        $courses = $this->courseRepository->findAll();
        require __DIR__ . '/../View/student.php';
    }
}
