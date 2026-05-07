<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class StudentsController extends Controller
{
    public function index()
    {
        $base     = config('api.base_url');
        $students = Http::get("{$base}/api/students")->json('data', []);
        $subjects = Http::get("{$base}/api/subjects")->json('data', []);

        return view('students.index', compact('students', 'subjects', 'base'));
    }
}
