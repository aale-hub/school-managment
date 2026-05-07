<?php

declare(strict_types=1);

$dbPath = __DIR__ . '/school.sqlite';
$pdo    = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('PRAGMA foreign_keys = ON;');

$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id         INTEGER PRIMARY KEY AUTOINCREMENT,
        name       TEXT    NOT NULL,
        email      TEXT    NOT NULL UNIQUE,
        created_at TEXT    NOT NULL
    );

    CREATE TABLE IF NOT EXISTS departments (
        id         INTEGER PRIMARY KEY AUTOINCREMENT,
        name       TEXT    NOT NULL,
        code       TEXT    NOT NULL UNIQUE,
        created_at TEXT    NOT NULL
    );

    CREATE TABLE IF NOT EXISTS courses (
        id         INTEGER PRIMARY KEY AUTOINCREMENT,
        name       TEXT    NOT NULL,
        code       TEXT    NOT NULL UNIQUE,
        credits    INTEGER NOT NULL,
        created_at TEXT    NOT NULL
    );

    CREATE TABLE IF NOT EXISTS teachers (
        id            INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id       INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
        specialty     TEXT    NOT NULL,
        department_id INTEGER REFERENCES departments(id) ON DELETE SET NULL,
        hired_at      TEXT    NOT NULL
    );

    CREATE TABLE IF NOT EXISTS students (
        id                INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id           INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
        enrollment_number TEXT    NOT NULL UNIQUE,
        course_id         INTEGER REFERENCES courses(id) ON DELETE SET NULL,
        enrolled_at       TEXT    NOT NULL
    );
");

echo "Base de datos creada en: {$dbPath}" . PHP_EOL;
echo "Tablas: users, departments, courses, teachers, students" . PHP_EOL;
