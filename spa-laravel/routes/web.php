<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\TeachersController;
use App\Http\Controllers\SubjectsController;
use App\Http\Controllers\DepartmentsController;
use App\Http\Controllers\Auth\GoogleController;

// Login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Rutas OAuth
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');

// Rutas protegidas
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/students', [StudentsController::class, 'index'])->name('students.index');
    Route::get('/teachers', [TeachersController::class, 'index'])->name('teachers.index');
    Route::get('/subjects', [SubjectsController::class, 'index'])->name('subjects.index');
    Route::get('/departments', [DepartmentsController::class, 'index'])->name('departments.index');
});