<?php
declare(strict_types=1);

namespace School\Infrastructure\Controller;

use School\Domain\Repository\StudentRepositoryInterface;
use School\Domain\Repository\TeacherRepositoryInterface;
use School\Domain\Repository\DepartmentRepositoryInterface;
use School\Domain\Repository\CourseRepositoryInterface;
use School\Domain\Repository\UserRepositoryInterface;
use School\Domain\ValueObject\Email;

class CreateController
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

    public function showCreateStudentForm(): void
    {
        $courses = $this->courseRepository->findAll();
        $students = $this->studentRepository->findAll();
        require __DIR__ . '/../View/create_student_form.php';
    }

    public function createStudent(): void
    {
        $userName = $_POST['name'] ?? '';
        $userEmail = $_POST['email'] ?? '';
        $userEnrollmentNumber = $_POST['enrollment_number'] ?? '';
        $courseId = $_POST['course_id'] ?? '';
        $message = '';

        if (!empty($userName) && !empty($userEmail) && !empty($userEnrollmentNumber)) {
            $user = new \School\Domain\Entity\User(null, $userName, new Email($userEmail));
            $this->userRepository->save($user);
            if ($courseId !== ''){
                $student = new \School\Domain\Entity\Student(null, $user->getId(), $userEnrollmentNumber, (int)$courseId);
            } else {
                $student = new \School\Domain\Entity\Student(null, $user->getId(), $userEnrollmentNumber);
            }
            $this->studentRepository->save($student);
            $message = "Student created successfully!"; 
        }
        $courses = $this->courseRepository->findAll();
        $users = $this->userRepository->findAll();
        $students = $this->studentRepository->findAll();
        extract(compact('users', 'students', 'message'));
        require __DIR__ . '/../View/student.php';
    }
    public function showCreateTeacherForm(): void
    {
        $departments = $this->departmentRepository->findAll();
        require __DIR__ . '/../View/create_teacher_form.php';
    }
    public function createTeacher(): void
    {
        $userName = $_POST['name'] ?? '';
        $userEmail = $_POST['email'] ?? '';
        $specialization = $_POST['specialization'] ?? '';
        $departmentId = $_POST['department_id'] ?? '';
        $message = '';

        if (!empty($userName) && !empty($userEmail)) {
            $user = new \School\Domain\Entity\User(null, $userName, new Email($userEmail));
            $this->userRepository->save($user);
            if ($departmentId !== '') {
                $teacher = new \School\Domain\Entity\Teacher(null, $user->getId(), $specialization, (int)$departmentId);
            } else {
            $teacher = new \School\Domain\Entity\Teacher(null, $user->getId(), $specialization);
            }
            $this->teacherRepository->save($teacher);
            $message = "Teacher created successfully!";
        }
        $departments = $this->departmentRepository->findAll();
        $users = $this->userRepository->findAll();
        $teachers = $this->teacherRepository->findAll();
        extract(compact('users', 'teachers', 'message'));
        require __DIR__ . '/../View/teacher.php';
    }

    public function showCreateDepartmentForm(): void
    {
        require __DIR__ . '/../View/create_department_form.php';
    }
    public function createDepartment(): void
    {
        $name = $_POST['name'] ?? '';
        $code = $_POST['code'] ?? '';
        $message = '';

        if (!empty($name) && !empty($code)) {
            $department = new \School\Domain\Entity\Department(null, $name, $code);
            $this->departmentRepository->save($department);
            $message = "Department created successfully!";
        }
        $departments = $this->departmentRepository->findAll();
        extract(compact('departments', 'message'));
        require __DIR__ . '/../View/department.php';
    }

    public function showCreateCourseForm(): void
    {
        require __DIR__ . '/../View/create_course_form.php';
    }
    public function createCourse(): void
    {
        $name = $_POST['name'] ?? '';
        $code = $_POST['code'] ?? '';
        $credits = isset($_POST['credits']) ? (int)$_POST['credits'] : 0;
        $message = '';
        
        if (!empty($name) && !empty($code) && $credits > 0) {
            $course = new \School\Domain\Entity\Course(null, $name, $code, $credits);
            $this->courseRepository->save($course);
            $message = "Course created successfully!";
        }
        $courses = $this->courseRepository->findAll();
        extract(compact('courses', 'message'));
        require __DIR__ . '/../View/course.php';
    }

}
