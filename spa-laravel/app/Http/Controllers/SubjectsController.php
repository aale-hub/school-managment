<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class SubjectsController extends Controller
{
    public function index()
    {
        $base     = config('api.base_url');
        $subjects = Http::get("{$base}/api/subjects")->json('data', []);

        return view('subjects.index', compact('subjects', 'base'));
    }
}
