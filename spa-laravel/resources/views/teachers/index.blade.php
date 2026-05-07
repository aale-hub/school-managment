@extends('layouts.app')

@section('title', 'Profesores')
@section('page-title', 'Profesores')

@section('content')

<div id="error-box" class="alert alert-error" style="display:none;"></div>

<div class="toolbar">
    <div style="display:flex;align-items:center;gap:10px;">
        <input type="text" id="search-input" placeholder="Buscar por nombre o especialidad...">
        <span class="count" id="count-label"></span>
    </div>
    <button class="btn btn-primary" onclick="openCreate()">+ Nuevo profesor</button>
</div>

<div id="spinner" class="spinner">Cargando...</div>

<div class="card" id="table-wrap" style="display:none;">
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Especialidad</th>
                <th>Departamento</th>
                <th>Contratado</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="teachers-body"></tbody>
    </table>
</div>

<div class="overlay" id="modal-overlay">
    <div class="modal">
        <h2 id="modal-title">Nuevo profesor</h2>
        <div id="modal-error" class="alert alert-error" style="display:none;"></div>
        <div class="form-group">
            <label>Nombre completo *</label>
            <input type="text" id="f-name" placeholder="Jane Smith">
            <div class="field-error" id="e-name"></div>
        </div>
        <div class="form-group">
            <label>Email *</label>
            <input type="email" id="f-email" placeholder="jane@school.edu">
            <div class="field-error" id="e-email"></div>
        </div>
        <div class="form-group">
            <label>Especialidad *</label>
            <input type="text" id="f-specialty" placeholder="Mathematics">
            <div class="field-error" id="e-specialty"></div>
        </div>
        <div class="form-group">
            <label>Departamento</label>
            <select id="f-department">
                <option value="">— Sin asignar —</option>
                @foreach($departments as $dept)
                <option value="{{ $dept['id'] }}">{{ $dept['name'] }}</option>
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
const DEPARTMENTS = @json($departments);

let teachers = @json($teachers);
let editingId = null;

function deptName(id) {
    if (!id) return '—';
    const d = DEPARTMENTS.find(d => d.id == id);
    return d ? d.name : '—';
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
    const body = document.getElementById('teachers-body');
    document.getElementById('count-label').textContent = data.length + ' resultado(s)';

    if (data.length === 0) {
        body.innerHTML = '<tr class="empty-row"><td colspan="6">Sin resultados</td></tr>';
        return;
    }

    body.innerHTML = data.map(t => `
        <tr>
            <td>${escHtml(t.name)}</td>
            <td>${escHtml(t.email)}</td>
            <td>${escHtml(t.specialty)}</td>
            <td>${escHtml(deptName(t.department_id))}</td>
            <td>${escHtml(formatDate(t.hired_at))}</td>
            <td style="text-align:right;">
                <button class="action-link edit" onclick="openEdit(${t.id})">Editar</button>
                <button class="action-link delete" onclick="deleteTeacher(${t.id})">Eliminar</button>
            </td>
        </tr>
    `).join('');
}

async function fetchTeachers() {
    document.getElementById('spinner').style.display = 'block';
    document.getElementById('table-wrap').style.display = 'none';
    document.getElementById('error-box').style.display = 'none';

    try {
        const res = await axios.get(API + '/api/teachers');
        teachers = res.data.data ?? [];
        document.getElementById('spinner').style.display = 'none';
        document.getElementById('table-wrap').style.display = 'block';
        renderTable(teachers);
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
    ['f-name','f-email','f-specialty'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('f-department').value = '';
    ['e-name','e-email','e-specialty'].forEach(id => document.getElementById(id).textContent = '');
    hideError('modal-error');
}

function openCreate() {
    editingId = null;
    clearForm();
    document.getElementById('modal-title').textContent = 'Nuevo profesor';
    document.getElementById('save-btn').textContent = 'Crear';
    document.getElementById('modal-overlay').classList.add('open');
}

function openEdit(id) {
    const teacher = teachers.find(t => t.id === id);
    if (!teacher) return;
    editingId = id;
    clearForm();
    document.getElementById('f-name').value = teacher.name ?? '';
    document.getElementById('f-email').value = teacher.email ?? '';
    document.getElementById('f-specialty').value = teacher.specialty ?? '';
    document.getElementById('f-department').value = teacher.department_id ?? '';
    document.getElementById('modal-title').textContent = 'Editar profesor';
    document.getElementById('save-btn').textContent = 'Actualizar';
    document.getElementById('modal-overlay').classList.add('open');
}

function closeModal() {
    document.getElementById('modal-overlay').classList.remove('open');
}

function validate() {
    let valid = true;
    const name      = document.getElementById('f-name').value.trim();
    const email     = document.getElementById('f-email').value.trim();
    const specialty = document.getElementById('f-specialty').value.trim();

    document.getElementById('e-name').textContent      = name      ? '' : 'El nombre es obligatorio';
    document.getElementById('e-email').textContent     = email     ? '' : 'El email es obligatorio';
    document.getElementById('e-specialty').textContent = specialty ? '' : 'La especialidad es obligatoria';

    if (!name || !email || !specialty) valid = false;
    return valid;
}

async function save() {
    if (!validate()) return;

    const btn = document.getElementById('save-btn');
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    const payload = {
        name:      document.getElementById('f-name').value.trim(),
        email:     document.getElementById('f-email').value.trim(),
        specialty: document.getElementById('f-specialty').value.trim(),
    };
    const deptId = document.getElementById('f-department').value;
    if (deptId) payload.department_id = parseInt(deptId);

    try {
        if (editingId) {
            await axios.put(API + '/api/teachers/' + editingId, payload);
        } else {
            await axios.post(API + '/api/teachers', payload);
        }
        closeModal();
        await fetchTeachers();
    } catch (e) {
        const msg = e.response?.data?.message ?? 'Error al guardar';
        showError('modal-error', msg);
    } finally {
        btn.disabled = false;
        btn.textContent = editingId ? 'Actualizar' : 'Crear';
    }
}

async function deleteTeacher(id) {
    if (!confirm('¿Eliminar este profesor?')) return;
    try {
        await axios.delete(API + '/api/teachers/' + id);
        teachers = teachers.filter(t => t.id !== id);
        renderTable(applySearch());
    } catch (e) {
        alert('Error al eliminar: ' + (e.response?.data?.message ?? e.message));
    }
}

function applySearch() {
    const q = document.getElementById('search-input').value.toLowerCase();
    if (!q) return teachers;
    return teachers.filter(t =>
        (t.name ?? '').toLowerCase().includes(q) ||
        (t.specialty ?? '').toLowerCase().includes(q)
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
renderTable(teachers);
document.getElementById('count-label').textContent = teachers.length + ' resultado(s)';

fetchTeachers();
</script>
@endpush
