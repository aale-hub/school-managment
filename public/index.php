<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// ─── Web controllers (existents) ──────────────────────────────────────────
use School\Infrastructure\Routing\Router;
use School\Infrastructure\Controller\HomeController;
use School\Infrastructure\Controller\DeleteController;
use School\Infrastructure\Controller\CreateController;
use School\Infrastructure\Controller\AssignmentController;

// ─── API controllers (nous) ───────────────────────────────────────────────
use School\Infrastructure\Routing\ApiRouter;
use School\Infrastructure\Controller\TeacherApiController;
use School\Infrastructure\Controller\StudentApiController;
use School\Infrastructure\Controller\SubjectApiController;
use School\Infrastructure\Controller\DepartmentApiController;
use School\Infrastructure\Api\ApiResponse;

use School\Infrastructure\Persistence\SQLite\SQLiteUserRepository;
use School\Infrastructure\Persistence\SQLite\SQLiteTeacherRepository;
use School\Infrastructure\Persistence\SQLite\SQLiteStudentRepository;
use School\Infrastructure\Persistence\SQLite\SQLiteDepartmentRepository;
use School\Infrastructure\Persistence\SQLite\SQLiteCourseRepository;

$userRepository       = new SQLiteUserRepository();
$teacherRepository    = new SQLiteTeacherRepository();
$studentRepository    = new SQLiteStudentRepository();
$departmentRepository = new SQLiteDepartmentRepository();
$courseRepository     = new SQLiteCourseRepository();

// ═════════════════════════════════════════════════════════════════════════
// DETECCIÓ: és una petició /api/* ?
// ═════════════════════════════════════════════════════════════════════════
$requestUri    = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestPath   = parse_url($requestUri, PHP_URL_PATH);
$isApiRequest  = str_starts_with($requestPath, '/api');

// ─── Capçaleres CORS per a totes les peticions API ────────────────────────
if ($isApiRequest) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    if ($requestMethod === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

// ═════════════════════════════════════════════════════════════════════════
// BLOC API  →  /api/*
// ═════════════════════════════════════════════════════════════════════════
if ($isApiRequest) {

    $teacherApiController = new TeacherApiController(
        $teacherRepository,
        $userRepository,
        $departmentRepository,
    );
    $studentApiController = new StudentApiController(
        $studentRepository,
        $userRepository,
        $courseRepository,
    );
    $subjectApiController = new SubjectApiController(
        $courseRepository,
    );
    $departmentApiController = new DepartmentApiController(
        $departmentRepository,
    );

    $apiRouter = new ApiRouter();

    $apiRouter->get('/api', function () {
        ApiResponse::success([
            'name'      => 'School Management API',
            'version'   => '1.0.0',
            'endpoints' => [
                'teachers'    => '/api/teachers',
                'students'    => '/api/students',
                'subjects'    => '/api/subjects',
                'departments' => '/api/departments',
            ],
        ], 'Welcome to School Management API');
    });

    $apiRouter->get('/api/departments',
        fn() => $departmentApiController->index());
    $apiRouter->get('/api/departments/{id}',
        fn($p) => $departmentApiController->show((int)$p['id']));
    $apiRouter->post('/api/departments',
        fn() => $departmentApiController->store());
    $apiRouter->put('/api/departments/{id}',
        fn($p) => $departmentApiController->update((int)$p['id']));
    $apiRouter->delete('/api/departments/{id}',
        fn($p) => $departmentApiController->destroy((int)$p['id']));

    // ── Teachers ──────────────────────────────────────────────────────────
    $apiRouter->get('/api/teachers',
        fn() => $teacherApiController->index());
    $apiRouter->get('/api/teachers/{id}',
        fn($p) => $teacherApiController->show((int)$p['id']));
    $apiRouter->post('/api/teachers',
        fn() => $teacherApiController->store());
    $apiRouter->put('/api/teachers/{id}',
        fn($p) => $teacherApiController->update((int)$p['id']));
    $apiRouter->delete('/api/teachers/{id}',
        fn($p) => $teacherApiController->destroy((int)$p['id']));
    $apiRouter->post('/api/teachers/{id}/assign-department',
        fn($p) => $teacherApiController->assignDepartment((int)$p['id']));

    // ── Students ──────────────────────────────────────────────────────────
    $apiRouter->get('/api/students',
        fn() => $studentApiController->index());
    $apiRouter->get('/api/students/{id}',
        fn($p) => $studentApiController->show((int)$p['id']));
    $apiRouter->post('/api/students',
        fn() => $studentApiController->store());
    $apiRouter->put('/api/students/{id}',
        fn($p) => $studentApiController->update((int)$p['id']));
    $apiRouter->delete('/api/students/{id}',
        fn($p) => $studentApiController->destroy((int)$p['id']));
    $apiRouter->post('/api/students/{id}/assign-course',
        fn($p) => $studentApiController->assignCourse((int)$p['id']));

    // ── Subjects ──────────────────────────────────────────────────────────
    $apiRouter->get('/api/subjects',
        fn() => $subjectApiController->index());
    $apiRouter->get('/api/subjects/{id}',
        fn($p) => $subjectApiController->show((int)$p['id']));
    $apiRouter->post('/api/subjects',
        fn() => $subjectApiController->store());
    $apiRouter->put('/api/subjects/{id}',
        fn($p) => $subjectApiController->update((int)$p['id']));
    $apiRouter->delete('/api/subjects/{id}',
        fn($p) => $subjectApiController->destroy((int)$p['id']));

    $apiRouter->dispatch($requestMethod, $requestUri);
    exit; // <-- Atura aquí, no continua cap a les rutes web
}

// ═════════════════════════════════════════════════════════════════════════
// BLOC WEB  →  tot el que NO és /api/*  (el teu codi original intacte)
// ═════════════════════════════════════════════════════════════════════════

$homeController = new HomeController(
    $studentRepository,
    $teacherRepository,
    $departmentRepository,
    $courseRepository,
    $userRepository
);
$createController = new CreateController(
    $studentRepository,
    $teacherRepository,
    $departmentRepository,
    $courseRepository,
    $userRepository
);
$assignmentController = new AssignmentController(
    $userRepository,
    $teacherRepository,
    $studentRepository,
    $departmentRepository,
    $courseRepository
);
$deleteController = new DeleteController(
    $studentRepository,
    $teacherRepository,
    $departmentRepository,
    $courseRepository,
    $userRepository
);

$router = new Router();

$router->get('/', function () use ($homeController) {
    $homeController->home();
});

// Rutas principales
$router->get('/student', function () use ($homeController) {
    $homeController->student();
});
$router->get('/teacher', function () use ($homeController) {
    $homeController->teacher();
});
$router->get('/department', function () use ($homeController) {
    $homeController->department();
});
$router->get('/course', function () use ($homeController) {
    $homeController->course();
});

// Crear
$router->get('/create-student', function () use ($createController) {
    $createController->showCreateStudentForm();
});
$router->post('/create-student', function () use ($createController) {
    $createController->createStudent();
});
$router->get('/create-teacher', function () use ($createController) {
    $createController->showCreateTeacherForm();
});
$router->post('/create-teacher', function () use ($createController) {
    $createController->createTeacher();
});
$router->get('/create-course', function () use ($createController) {
    $createController->showCreateCourseForm();
});
$router->post('/create-course', function () use ($createController) {
    $createController->createCourse();
});
$router->get('/create-department', function () use ($createController) {
    $createController->showCreateDepartmentForm();
});
$router->post('/create-department', function () use ($createController) {
    $createController->createDepartment();
});

// Assignar
$router->get('/assign-teacher', function () use ($assignmentController) {
    $assignmentController->showAssignTeacherForm();
});
$router->post('/assign-teacher', function () use ($assignmentController) {
    $assignmentController->assignTeacher();
});
$router->get('/assign-student', function () use ($assignmentController) {
    $assignmentController->showAssignStudentForm();
});
$router->post('/assign-student', function () use ($assignmentController) {
    $assignmentController->assignStudent();
});

// Eliminar
$router->get('/delete-student', function () use ($deleteController) {
    $deleteController->deleteStudent();
});
$router->get('/delete-teacher', function () use ($deleteController) {
    $deleteController->deleteTeacher();
});
$router->get('/delete-department', function () use ($deleteController) {
    $deleteController->deleteDepartment();
});
$router->get('/delete-course', function () use ($deleteController) {
    $deleteController->deleteCourse();
});

// Ruta per defecte
$router->get('/', function () use ($homeController) {
    $homeController->student();
});

// Despachar la solicitud web
$router->dispatch($requestMethod, $requestUri);
