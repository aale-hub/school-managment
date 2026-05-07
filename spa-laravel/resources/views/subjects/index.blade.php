@extends('layouts.app')

@section('title', 'Asignaturas')
@section('page-title', 'Asignaturas')

@section('content')

<div id="error-box" class="alert alert-error" style="display:none;"></div>

<div class="toolbar">
    <div style="display:flex;align-items:center;gap:10px;">
        <input type="text" id="search-input" placeholder="Buscar por nombre o código...">
        <span class="count" id="count-label"></span>
    </div>
    <button class="btn btn-primary" onclick="openCreate()">+ Nueva asignatura</button>
</div>

<div id="spinner" class="spinner">Cargando...</div>

<div class="card" id="table-wrap" style="display:none;">
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Código</th>
                <th>Créditos</th>
                <th>Creada</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="subjects-body"></tbody>
    </table>
</div>

<div class="overlay" id="modal-overlay">
    <div class="modal">
        <h2 id="modal-title">Nueva asignatura</h2>
        <div id="modal-error" class="alert alert-error" style="display:none;"></div>
        <div class="form-group">
            <label>Nombre *</label>
            <input type="text" id="f-name" placeholder="Calculus I">
            <div class="field-error" id="e-name"></div>
        </div>
        <div class="form-group">
            <label>Código *</label>
            <input type="text" id="f-code" placeholder="MATH101">
            <div class="field-error" id="e-code"></div>
        </div>
        <div class="form-group">
            <label>Créditos *</label>
            <input type="number" id="f-credits" min="1" max="12" value="3">
            <div class="field-error" id="e-credits"></div>
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

let subjects = @json($subjects);
let editingId = null;

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
    const body = document.getElementById('subjects-body');
    document.getElementById('count-label').textContent = data.length + ' resultado(s)';

    if (data.length === 0) {
        body.innerHTML = '<tr class="empty-row"><td colspan="5">Sin resultados</td></tr>';
        return;
    }

    body.innerHTML = data.map(s => `
        <tr>
            <td>${escHtml(s.name)}</td>
            <td><span class="badge">${escHtml(s.code)}</span></td>
            <td>${escHtml(s.credits)}</td>
            <td>${escHtml(formatDate(s.created_at))}</td>
            <td style="text-align:right;">
                <button class="action-link edit" onclick="openEdit(${s.id})">Editar</button>
                <button class="action-link delete" onclick="deleteSubject(${s.id})">Eliminar</button>
            </td>
        </tr>
    `).join('');
}

async function fetchSubjects() {
    document.getElementById('spinner').style.display = 'block';
    document.getElementById('table-wrap').style.display = 'none';
    document.getElementById('error-box').style.display = 'none';

    try {
        const res = await axios.get(API + '/api/subjects');
        subjects = res.data.data ?? [];
        document.getElementById('spinner').style.display = 'none';
        document.getElementById('table-wrap').style.display = 'block';
        renderTable(subjects);
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
    document.getElementById('f-name').value = '';
    document.getElementById('f-code').value = '';
    document.getElementById('f-credits').value = '3';
    ['e-name','e-code','e-credits'].forEach(id => document.getElementById(id).textContent = '');
    hideError('modal-error');
}

function openCreate() {
    editingId = null;
    clearForm();
    document.getElementById('modal-title').textContent = 'Nueva asignatura';
    document.getElementById('save-btn').textContent = 'Crear';
    document.getElementById('modal-overlay').classList.add('open');
}

function openEdit(id) {
    const subject = subjects.find(s => s.id === id);
    if (!subject) return;
    editingId = id;
    clearForm();
    document.getElementById('f-name').value    = subject.name ?? '';
    document.getElementById('f-code').value    = subject.code ?? '';
    document.getElementById('f-credits').value = subject.credits ?? 3;
    document.getElementById('modal-title').textContent = 'Editar asignatura';
    document.getElementById('save-btn').textContent = 'Actualizar';
    document.getElementById('modal-overlay').classList.add('open');
}

function closeModal() {
    document.getElementById('modal-overlay').classList.remove('open');
}

function validate() {
    let valid = true;
    const name    = document.getElementById('f-name').value.trim();
    const code    = document.getElementById('f-code').value.trim();
    const credits = parseInt(document.getElementById('f-credits').value);

    document.getElementById('e-name').textContent    = name              ? '' : 'El nombre es obligatorio';
    document.getElementById('e-code').textContent    = code              ? '' : 'El código es obligatorio';
    document.getElementById('e-credits').textContent = credits >= 1      ? '' : 'Los créditos deben ser >= 1';

    if (!name || !code || credits < 1) valid = false;
    return valid;
}

async function save() {
    if (!validate()) return;

    const btn = document.getElementById('save-btn');
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    const payload = {
        name:    document.getElementById('f-name').value.trim(),
        code:    document.getElementById('f-code').value.trim(),
        credits: parseInt(document.getElementById('f-credits').value),
    };

    try {
        if (editingId) {
            await axios.put(API + '/api/subjects/' + editingId, payload);
        } else {
            await axios.post(API + '/api/subjects', payload);
        }
        closeModal();
        await fetchSubjects();
    } catch (e) {
        const msg = e.response?.data?.message ?? 'Error al guardar';
        showError('modal-error', msg);
    } finally {
        btn.disabled = false;
        btn.textContent = editingId ? 'Actualizar' : 'Crear';
    }
}

async function deleteSubject(id) {
    if (!confirm('¿Eliminar esta asignatura?')) return;
    try {
        await axios.delete(API + '/api/subjects/' + id);
        subjects = subjects.filter(s => s.id !== id);
        renderTable(applySearch());
    } catch (e) {
        alert('Error al eliminar: ' + (e.response?.data?.message ?? e.message));
    }
}

function applySearch() {
    const q = document.getElementById('search-input').value.toLowerCase();
    if (!q) return subjects;
    return subjects.filter(s =>
        (s.name ?? '').toLowerCase().includes(q) ||
        (s.code ?? '').toLowerCase().includes(q)
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
renderTable(subjects);
document.getElementById('count-label').textContent = subjects.length + ' resultado(s)';

fetchSubjects();
</script>
@endpush
