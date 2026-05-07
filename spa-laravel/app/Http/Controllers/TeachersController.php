<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class TeachersController extends Controller
{
    public function index()
    {
        $base        = config('api.base_url');
        $teachers    = Http::get("{$base}/api/teachers")->json('data', []);
        $departments = Http::get("{$base}/api/departments")->json('data', []);

        return view('teachers.index', compact('teachers', 'departments', 'base'));
    }
}
