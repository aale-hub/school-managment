<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\TeachersController;
use App\Http\Controllers\SubjectsController;
use App\Http\Controllers\DepartmentsController;

Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::get('/students', [StudentsController::class, 'index'])->name('students.index');
Route::get('/teachers', [TeachersController::class, 'index'])->name('teachers.index');
Route::get('/subjects', [SubjectsController::class, 'index'])->name('subjects.index');
Route::get('/departments', [DepartmentsController::class, 'index'])->name('departments.index');
