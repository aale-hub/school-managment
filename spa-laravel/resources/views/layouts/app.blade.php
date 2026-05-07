<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'School Management')</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>

<nav>
    <div class="nav-brand">
        School Management
        <span>SPA Frontend</span>
    </div>
    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Dashboard</a>
    <a href="{{ route('students.index') }}" class="{{ request()->routeIs('students.*') ? 'active' : '' }}">Estudiantes</a>
    <a href="{{ route('teachers.index') }}" class="{{ request()->routeIs('teachers.*') ? 'active' : '' }}">Profesores</a>
    <a href="{{ route('subjects.index') }}" class="{{ request()->routeIs('subjects.*') ? 'active' : '' }}">Asignaturas</a>
    <a href="{{ route('departments.index') }}" class="{{ request()->routeIs('departments.*') ? 'active' : '' }}">Departamentos</a>
    <div class="nav-footer">API: {{ config('api.base_url') }}</div>
</nav>

<div class="main">
    <div class="main-header">
        <h1>@yield('page-title', 'Dashboard')</h1>
        @hasSection('page-subtitle')<p>@yield('page-subtitle')</p>@endif
    </div>
    <div class="content">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
@stack('scripts')
</body>
</html>
