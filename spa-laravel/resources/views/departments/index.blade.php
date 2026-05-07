@extends('layouts.app')

@section('title', 'Departamentos')
@section('page-title', 'Departamentos')

@section('content')

<div id="error-box" class="alert alert-error" style="display:none;"></div>

<div class="toolbar">
    <div style="display:flex;align-items:center;gap:10px;">
        <input type="text" id="search-input" placeholder="Buscar por nombre o código...">
        <span class="count" id="count-label"></span>
    </div>
    <button class="btn btn-primary" onclick="openCreate()">+ Nuevo departamento</button>
</div>

<div id="spinner" class="spinner">Cargando...</div>

<div class="card" id="table-wrap" style="display:none;">
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Código</th>
                <th>Creado</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="departments-body"></tbody>
    </table>
</div>

<div class="overlay" id="modal-overlay">
    <div class="modal">
        <h2 id="modal-title">Nuevo departamento</h2>
        <div id="modal-error" class="alert alert-error" style="display:none;"></div>
        <div class="form-group">
            <label>Nombre *</label>
            <input type="text" id="f-name" placeholder="Mathematics Department">
            <div class="field-error" id="e-name"></div>
        </div>
        <div class="form-group">
            <label>Código *</label>
            <input type="text" id="f-code" placeholder="MATH">
            <div class="field-error" id="e-code"></div>
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

let departments = @json($departments);
let editingId   = null;

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
    const body = document.getElementById('departments-body');
    document.getElementById('count-label').textContent = data.length + ' resultado(s)';

    if (data.length === 0) {
        body.innerHTML = '<tr class="empty-row"><td colspan="4">Sin resultados</td></tr>';
        return;
    }

    body.innerHTML = data.map(d => `
        <tr>
            <td>${escHtml(d.name)}</td>
            <td><span class="badge">${escHtml(d.code)}</span></td>
            <td>${escHtml(formatDate(d.created_at))}</td>
            <td style="text-align:right;">
                <button class="action-link edit" onclick="openEdit(${d.id})">Editar</button>
                <button class="action-link delete" onclick="deleteDepartment(${d.id})">Eliminar</button>
            </td>
        </tr>
    `).join('');
}

async function fetchDepartments() {
    document.getElementById('spinner').style.display = 'block';
    document.getElementById('table-wrap').style.display = 'none';
    document.getElementById('error-box').style.display = 'none';

    try {
        const res = await axios.get(API + '/api/departments');
        departments = res.data.data ?? [];
        document.getElementById('spinner').style.display = 'none';
        document.getElementById('table-wrap').style.display = 'block';
        renderTable(departments);
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
    document.getElementById('e-name').textContent = '';
    document.getElementById('e-code').textContent = '';
    hideError('modal-error');
}

function openCreate() {
    editingId = null;
    clearForm();
    document.getElementById('modal-title').textContent = 'Nuevo departamento';
    document.getElementById('save-btn').textContent = 'Crear';
    document.getElementById('modal-overlay').classList.add('open');
}

function openEdit(id) {
    const dept = departments.find(d => d.id === id);
    if (!dept) return;
    editingId = id;
    clearForm();
    document.getElementById('f-name').value = dept.name ?? '';
    document.getElementById('f-code').value = dept.code ?? '';
    document.getElementById('modal-title').textContent = 'Editar departamento';
    document.getElementById('save-btn').textContent = 'Actualizar';
    document.getElementById('modal-overlay').classList.add('open');
}

function closeModal() {
    document.getElementById('modal-overlay').classList.remove('open');
}

function validate() {
    const name = document.getElementById('f-name').value.trim();
    const code = document.getElementById('f-code').value.trim();

    document.getElementById('e-name').textContent = name ? '' : 'El nombre es obligatorio';
    document.getElementById('e-code').textContent = code ? '' : 'El código es obligatorio';

    return name && code;
}

async function save() {
    if (!validate()) return;

    const btn = document.getElementById('save-btn');
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    const payload = {
        name: document.getElementById('f-name').value.trim(),
        code: document.getElementById('f-code').value.trim(),
    };

    try {
        if (editingId) {
            await axios.put(API + '/api/departments/' + editingId, payload);
        } else {
            await axios.post(API + '/api/departments', payload);
        }
        closeModal();
        await fetchDepartments();
    } catch (e) {
        const msg = e.response?.data?.message ?? 'Error al guardar';
        showError('modal-error', msg);
    } finally {
        btn.disabled = false;
        btn.textContent = editingId ? 'Actualizar' : 'Crear';
    }
}

async function deleteDepartment(id) {
    if (!confirm('¿Eliminar este departamento?')) return;
    try {
        await axios.delete(API + '/api/departments/' + id);
        departments = departments.filter(d => d.id !== id);
        renderTable(applySearch());
    } catch (e) {
        alert('Error al eliminar: ' + (e.response?.data?.message ?? e.message));
    }
}

function applySearch() {
    const q = document.getElementById('search-input').value.toLowerCase();
    if (!q) return departments;
    return departments.filter(d =>
        (d.name ?? '').toLowerCase().includes(q) ||
        (d.code ?? '').toLowerCase().includes(q)
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
renderTable(departments);
document.getElementById('count-label').textContent = departments.length + ' resultado(s)';

fetchDepartments();
</script>
@endpush
