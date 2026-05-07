<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class DepartmentsController extends Controller
{
    public function index()
    {
        $base        = config('api.base_url');
        $departments = Http::get("{$base}/api/departments")->json('data', []);

        return view('departments.index', compact('departments', 'base'));
    }
}
