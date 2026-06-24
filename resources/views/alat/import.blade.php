{{-- resources/views/alat/import.blade.php --}}
@extends('layouts.app')

@section('title', 'Import Alat via Excel')
@section('page-title', 'Import Alat via Excel')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('alat.index') }}">Alat Kerja</a></li>
    <li class="breadcrumb-item active">Import Excel</li>
@endsection

@section('content')
<div class="row">

    {{-- ── Form Upload ─────────────────────────────── --}}
    <div class="col-md-7">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-excel mr-2 text-success"></i>Upload File Excel
                </h3>
            </div>

            <form action="{{ route('alat.import.preview') }}" method="POST"
                  enctype="multipart/form-data" id="importForm">
                @csrf
                <div class="card-body">

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    @endif

                    @error('file_excel')
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>{{ $message }}
                    </div>
                    @enderror

                    {{-- Drop zone --}}
                    <div class="drop-zone" id="dropZone">
                        <div class="drop-zone-inner" id="dropContent">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <p class="mb-1 font-weight-bold">Seret & letakkan file di sini</p>
                            <p class="text-muted small mb-3">atau klik untuk memilih file</p>
                            <label for="file_excel" class="btn btn-outline-success btn-sm mb-0">
                                <i class="fas fa-folder-open mr-1"></i>Pilih File Excel
                            </label>
                            <input type="file" id="file_excel" name="file_excel"
                                   accept=".xlsx,.xls" class="d-none">
                        </div>
                        <div id="fileInfo" class="d-none text-center">
                            <i class="fas fa-file-excel fa-3x text-success mb-3"></i>
                            <p class="font-weight-bold mb-1" id="fileName">—</p>
                            <p class="text-muted small mb-3" id="fileSize">—</p>
                            <button type="button" class="btn btn-outline-danger btn-sm" id="removeFile">
                                <i class="fas fa-times mr-1"></i>Hapus File
                            </button>
                        </div>
                    </div>

                    <p class="text-muted small mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Format yang diterima: <strong>.xlsx, .xls</strong> — Maksimal <strong>5 MB</strong> — Maksimal <strong>500 baris</strong> data
                    </p>
                </div>

                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div>
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                            <i class="fas fa-search mr-1"></i> Preview Data
                        </button>
                        <a href="{{ route('alat.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-times mr-1"></i> Batal
                        </a>
                    </div>
                    <a href="{{ route('alat.import.template') }}" class="btn btn-success">
                        <i class="fas fa-download mr-1"></i> Download Template
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Panduan ──────────────────────────────────── --}}
    <div class="col-md-5">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-question-circle mr-2"></i>Cara Import</h3>
            </div>
            <div class="card-body p-0">
                <div class="timeline timeline-inverse p-3">

                    <div class="time-label">
                        <span class="bg-primary">Langkah</span>
                    </div>

                    <div>
                        <i class="fas fa-download bg-success"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Download Template</h3>
                            <div class="timeline-body text-sm">
                                Klik tombol <strong>"Download Template"</strong> untuk mendapatkan file Excel dengan format yang benar. File berisi 3 sheet: <em>Template Import, Kode Kategori, dan Panduan Pengisian</em>.
                            </div>
                        </div>
                    </div>

                    <div>
                        <i class="fas fa-edit bg-warning"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Isi Data Alat</h3>
                            <div class="timeline-body text-sm">
                                Isi data alat mulai dari <strong>baris ke-5</strong>. Hapus baris contoh terlebih dahulu. Kolom wajib: <span class="badge badge-danger">Nama Alat</span> <span class="badge badge-danger">Kode Kategori</span> <span class="badge badge-danger">Status</span>.
                            </div>
                        </div>
                    </div>

                    <div>
                        <i class="fas fa-upload bg-primary"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Upload & Preview</h3>
                            <div class="timeline-body text-sm">
                                Upload file, sistem akan membaca dan menampilkan <strong>preview data</strong> beserta deteksi kesalahan sebelum disimpan ke database.
                            </div>
                        </div>
                    </div>

                    <div>
                        <i class="fas fa-save bg-success"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Konfirmasi & Simpan</h3>
                            <div class="timeline-body text-sm">
                                Periksa preview. Jika data sudah benar, klik <strong>"Simpan ke Database"</strong>. Kode alat akan digenerate otomatis oleh sistem.
                            </div>
                        </div>
                    </div>

                    <div>
                        <i class="fas fa-check bg-success"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kode Kategori quick ref --}}
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tags mr-2"></i>Kode Kategori</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="thead-light">
                        <tr><th>Kode</th><th>Kategori</th></tr>
                    </thead>
                    <tbody>
                        @php
                            $kategori = \App\Models\KategoriAlat::orderBy('kode')->get();
                        @endphp
                        @forelse($kategori as $k)
                        <tr>
                            <td><code>{{ $k->kode }}</code></td>
                            <td>{{ $k->nama }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center text-muted">Belum ada kategori</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
.drop-zone {
    border: 2.5px dashed #adb5bd;
    border-radius: 10px;
    padding: 40px 20px;
    text-align: center;
    cursor: pointer;
    transition: all .25s;
    background: #f8f9fa;
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.drop-zone.drag-over {
    border-color: #007bff;
    background: #e8f4ff;
}
.drop-zone.has-file {
    border-color: #28a745;
    background: #f0fff4;
}
</style>
@endpush

@push('scripts')
<script>
const dropZone   = document.getElementById('dropZone');
const fileInput  = document.getElementById('file_excel');
const dropContent= document.getElementById('dropContent');
const fileInfo   = document.getElementById('fileInfo');
const fileName   = document.getElementById('fileName');
const fileSize   = document.getElementById('fileSize');
const removeBtn  = document.getElementById('removeFile');
const submitBtn  = document.getElementById('submitBtn');

function formatBytes(bytes) {
    if (bytes < 1024)       return bytes + ' B';
    if (bytes < 1048576)    return (bytes/1024).toFixed(1) + ' KB';
    return (bytes/1048576).toFixed(1) + ' MB';
}

function showFile(file) {
    fileName.textContent = file.name;
    fileSize.textContent = formatBytes(file.size);
    dropContent.classList.add('d-none');
    fileInfo.classList.remove('d-none');
    dropZone.classList.add('has-file');
    submitBtn.disabled = false;
}

function clearFile() {
    fileInput.value = '';
    dropContent.classList.remove('d-none');
    fileInfo.classList.add('d-none');
    dropZone.classList.remove('has-file', 'drag-over');
    submitBtn.disabled = true;
}

// Click on drop zone
dropZone.addEventListener('click', (e) => {
    if (!e.target.closest('#removeFile') && !e.target.closest('label')) fileInput.click();
});

fileInput.addEventListener('change', () => {
    if (fileInput.files[0]) showFile(fileInput.files[0]);
});

removeBtn.addEventListener('click', (e) => { e.stopPropagation(); clearFile(); });

// Drag & drop
dropZone.addEventListener('dragover',  (e) => { e.preventDefault(); dropZone.classList.add('drag-over'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file && (file.name.endsWith('.xlsx') || file.name.endsWith('.xls'))) {
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
        showFile(file);
    } else {
        Swal.fire('Format Salah', 'Hanya file .xlsx atau .xls yang diperbolehkan.', 'error');
    }
});

// Loading saat submit
document.getElementById('importForm').addEventListener('submit', () => {
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Membaca File...';
});
</script>
@endpush
