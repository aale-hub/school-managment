<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        $base = config('api.base_url');

        $students = Http::get("{$base}/api/students")->json('data', []);
        $teachers = Http::get("{$base}/api/teachers")->json('data', []);
        $subjects = Http::get("{$base}/api/subjects")->json('data', []);

        return view('dashboard', compact('students', 'teachers', 'subjects'));
    }
}
