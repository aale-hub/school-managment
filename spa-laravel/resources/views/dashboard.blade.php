@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<div class="stats">
    <a href="{{ route('students.index') }}" class="stat-card">
        <div>
            <div class="stat-number">{{ count($students) }}</div>
            <div class="stat-label">Estudiantes</div>
        </div>
    </a>
    <a href="{{ route('teachers.index') }}" class="stat-card">
        <div>
            <div class="stat-number">{{ count($teachers) }}</div>
            <div class="stat-label">Profesores</div>
        </div>
    </a>
    <a href="{{ route('subjects.index') }}" class="stat-card">
        <div>
            <div class="stat-number">{{ count($subjects) }}</div>
            <div class="stat-label">Asignaturas</div>
        </div>
    </a>
</div>

@if(empty($students) && empty($teachers) && empty($subjects))
<div class="alert alert-info">
    No se pudo conectar con la API. Asegúrate de que el servidor PHP esté corriendo en <strong>{{ config('api.base_url') }}</strong>.
</div>
@endif

<div class="summary-tables">
    <div class="card">
        <div class="summary-table-title">
            Estudiantes recientes
            <a href="{{ route('students.index') }}">Ver todos</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Matrícula</th>
                </tr>
            </thead>
            <tbody>
                @forelse(array_slice($students, 0, 5) as $student)
                <tr>
                    <td>{{ $student['name'] }}</td>
                    <td><span class="badge">{{ $student['enrollment_number'] }}</span></td>
                </tr>
                @empty
                <tr class="empty-row"><td colspan="2">Sin datos</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <div class="summary-table-title">
            Profesores recientes
            <a href="{{ route('teachers.index') }}">Ver todos</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Especialidad</th>
                </tr>
            </thead>
            <tbody>
                @forelse(array_slice($teachers, 0, 5) as $teacher)
                <tr>
                    <td>{{ $teacher['name'] }}</td>
                    <td>{{ $teacher['specialty'] }}</td>
                </tr>
                @empty
                <tr class="empty-row"><td colspan="2">Sin datos</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
