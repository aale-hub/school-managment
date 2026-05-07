@extends('layouts.app')

@section('title', 'Estudiantes')
@section('page-title', 'Estudiantes')

@section('content')

<div id="error-box" class="alert alert-error" style="display:none;"></div>

<div class="toolbar">
    <div style="display:flex;align-items:center;gap:10px;">
        <input type="text" id="search-input" placeholder="Buscar por nombre o matrícula...">
        <span class="count" id="count-label"></span>
    </div>
    <button class="btn btn-primary" onclick="openCreate()">+ Nuevo estudiante</button>
</div>

<div id="spinner" class="spinner">Cargando...</div>

<div class="card" id="table-wrap" style="display:none;">
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Matrícula</th>
                <th>Asignatura</th>
                <th>Inscrito</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="students-body"></tbody>
    </table>
</div>

<div class="overlay" id="modal-overlay">
    <div class="modal">
        <h2 id="modal-title">Nuevo estudiante</h2>
        <div id="modal-error" class="alert alert-error" style="display:none;"></div>
        <div class="form-group">
            <label>Nombre completo *</label>
            <input type="text" id="f-name" placeholder="John Doe">
            <div class="field-error" id="e-name"></div>
        </div>
        <div class="form-group">
            <label>Email *</label>
            <input type="email" id="f-email" placeholder="john@school.edu">
            <div class="field-error" id="e-email"></div>
        </div>
        <div class="form-group">
            <label>Matrícula *</label>
            <input type="text" id="f-enrollment" placeholder="STU-2024-003">
            <div class="field-error" id="e-enrollment"></div>
        </div>
        <div class="form-group">
            <label>Asignatura</label>
            <select id="f-course">
                <option value="">— Sin asignar —</option>
                @foreach($subjects as $subject)
                <option value="{{ $subject['id'] }}">{{ $subject['name'] }} ({{ $subject['code'] }})</option>
                @endforeach
            </select>
        </div>
        <div class="modal-actions">
            <button class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
            <button class="btn btn-primary" id="save-btn" onclick="save()">Crear</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const API = '{{ $base }}';
const SUBJECTS = @json($subjects);

let students = @json($students);
let editingId = null;

function subjectName(id) {
    if (!id) return '—';
    const s = SUBJECTS.find(s => s.id == id);
    return s ? s.name : '—';
}

function formatDate(dt) {
    if (!dt) return '';
    return new Date(dt).toLocaleDateString('es-ES');
}

function escHtml(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str ?? ''));
    return d.innerHTML;
}

function renderTable(data) {
    const body = document.getElementById('students-body');
    document.getElementById('count-label').textContent = data.length + ' resultado(s)';

    if (data.length === 0) {
        body.innerHTML = '<tr class="empty-row"><td colspan="6">Sin resultados</td></tr>';
        return;
    }

    body.innerHTML = data.map(s => `
        <tr>
            <td>${escHtml(s.name)}</td>
            <td>${escHtml(s.email)}</td>
            <td><span class="badge">${escHtml(s.enrollment_number)}</span></td>
            <td>${escHtml(subjectName(s.course_id))}</td>
            <td>${escHtml(formatDate(s.enrolled_at))}</td>
            <td style="text-align:right;">
                <button class="action-link edit" onclick="openEdit(${s.id})">Editar</button>
                <button class="action-link delete" onclick="deleteStudent(${s.id})">Eliminar</button>
            </td>
        </tr>
    `).join('');
}

async function fetchStudents() {
    document.getElementById('spinner').style.display = 'block';
    document.getElementById('table-wrap').style.display = 'none';
    document.getElementById('error-box').style.display = 'none';

    try {
        const res = await axios.get(API + '/api/students');
        students = res.data.data ?? [];
        document.getElementById('spinner').style.display = 'none';
        document.getElementById('table-wrap').style.display = 'block';
        renderTable(students);
    } catch (e) {
        document.getElementById('spinner').style.display = 'none';
        showError('error-box', 'No se pudo conectar con la API.');
    }
}

function showError(id, msg) {
    const el = document.getElementById(id);
    el.textContent = msg;
    el.style.display = 'block';
}

function hideError(id) {
    document.getElementById(id).style.display = 'none';
}

function clearForm() {
    ['f-name','f-email','f-enrollment'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('f-course').value = '';
    ['e-name','e-email','e-enrollment'].forEach(id => document.getElementById(id).textContent = '');
    hideError('modal-error');
}

function openCreate() {
    editingId = null;
    clearForm();
    document.getElementById('modal-title').textContent = 'Nuevo estudiante';
    document.getElementById('save-btn').textContent = 'Crear';
    document.getElementById('modal-overlay').classList.add('open');
}

function openEdit(id) {
    const student = students.find(s => s.id === id);
    if (!student) return;
    editingId = id;
    clearForm();
    document.getElementById('f-name').value = student.name ?? '';
    document.getElementById('f-email').value = student.email ?? '';
    document.getElementById('f-enrollment').value = student.enrollment_number ?? '';
    document.getElementById('f-course').value = student.course_id ?? '';
    document.getElementById('modal-title').textContent = 'Editar estudiante';
    document.getElementById('save-btn').textContent = 'Actualizar';
    document.getElementById('modal-overlay').classList.add('open');
}

function closeModal() {
    document.getElementById('modal-overlay').classList.remove('open');
}

function validate() {
    let valid = true;
    const name = document.getElementById('f-name').value.trim();
    const email = document.getElementById('f-email').value.trim();
    const enrollment = document.getElementById('f-enrollment').value.trim();

    document.getElementById('e-name').textContent = name ? '' : 'El nombre es obligatorio';
    document.getElementById('e-email').textContent = email ? '' : 'El email es obligatorio';
    document.getElementById('e-enrollment').textContent = enrollment ? '' : 'La matrícula es obligatoria';

    if (!name || !email || !enrollment) valid = false;
    return valid;
}

async function save() {
    if (!validate()) return;

    const btn = document.getElementById('save-btn');
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    const payload = {
        name: document.getElementById('f-name').value.trim(),
        email: document.getElementById('f-email').value.trim(),
        enrollment_number: document.getElementById('f-enrollment').value.trim(),
    };
    const courseId = document.getElementById('f-course').value;
    if (courseId) payload.course_id = parseInt(courseId);

    try {
        if (editingId) {
            await axios.put(API + '/api/students/' + editingId, payload);
        } else {
            await axios.post(API + '/api/students', payload);
        }
        closeModal();
        await fetchStudents();
    } catch (e) {
        const msg = e.response?.data?.message ?? 'Error al guardar';
        showError('modal-error', msg);
    } finally {
        btn.disabled = false;
        btn.textContent = editingId ? 'Actualizar' : 'Crear';
    }
}

async function deleteStudent(id) {
    if (!confirm('¿Eliminar este estudiante?')) return;
    try {
        await axios.delete(API + '/api/students/' + id);
        students = students.filter(s => s.id !== id);
        renderTable(applySearch());
    } catch (e) {
        alert('Error al eliminar: ' + (e.response?.data?.message ?? e.message));
    }
}

function applySearch() {
    const q = document.getElementById('search-input').value.toLowerCase();
    if (!q) return students;
    return students.filter(s =>
        (s.name ?? '').toLowerCase().includes(q) ||
        (s.enrollment_number ?? '').toLowerCase().includes(q)
    );
}

document.getElementById('search-input').addEventListener('input', () => {
    renderTable(applySearch());
});

document.getElementById('modal-overlay').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModal();
});

document.getElementById('spinner').style.display = 'none';
document.getElementById('table-wrap').style.display = 'block';
renderTable(students);
document.getElementById('count-label').textContent = students.length + ' resultado(s)';

fetchStudents();
</script>
@endpush
