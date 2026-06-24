{{-- resources/views/alat/import-preview.blade.php --}}
@extends('layouts.app')

@section('title', 'Preview Import Alat')
@section('page-title', 'Preview Data Import')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('alat.index') }}">Alat Kerja</a></li>
    <li class="breadcrumb-item"><a href="{{ route('alat.import.form') }}">Import Excel</a></li>
    <li class="breadcrumb-item active">Preview</li>
@endsection

@section('content')

{{-- ── Summary Bar ──────────────────────────────────── --}}
<div class="row mb-3">
    <div class="col-sm-4">
        <div class="info-box shadow-sm mb-2">
            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Siap Disimpan</span>
                <span class="info-box-number">{{ count($preview) }}</span>
                <span class="info-box-text text-sm">baris data valid</span>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="info-box shadow-sm mb-2">
            <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Baris Bermasalah</span>
                <span class="info-box-number">{{ count($errors) }}</span>
                <span class="info-box-text text-sm">baris akan dilewati</span>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="info-box shadow-sm mb-2">
            <span class="info-box-icon bg-info"><i class="fas fa-list"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Dibaca</span>
                <span class="info-box-number">{{ count($preview) + count($errors) }}</span>
                <span class="info-box-text text-sm">baris total</span>
            </div>
        </div>
    </div>
</div>

{{-- ── Error detail ────────────────────────────────────── --}}
@if(count($errors) > 0)
<div class="card card-outline card-danger">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-exclamation-triangle mr-2 text-danger"></i>
            {{ count($errors) }} Baris dengan Kesalahan
            <small class="text-muted ml-1">(baris ini tidak akan disimpan)</small>
        </h3>
        <div class="card-tools">
            <button class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-sm table-hover mb-0">
            <thead class="thead-light">
                <tr>
                    <th width="80">Baris Excel</th>
                    <th>Detail Kesalahan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($errors as $err)
                <tr>
                    <td class="text-center font-weight-bold text-danger">Baris {{ $err['baris'] }}</td>
                    <td>
                        @foreach($err['pesan'] as $p)
                            <span class="badge badge-danger mb-1">{{ $p }}</span>
                        @endforeach
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ── Preview Data Valid ──────────────────────────────── --}}
@if(count($preview) > 0)
<div class="card card-outline card-success">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-table mr-2 text-success"></i>
            Preview {{ count($preview) }} Data yang Akan Disimpan
        </h3>
        <div class="card-tools">
            <button class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div style="overflow-x:auto">
        <table class="table table-bordered table-hover table-sm mb-0">
            <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th>Nama Alat</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Merk</th>
                    <th>No. Seri</th>
                    <th>Lokasi</th>
                    <th>Tgl Beli</th>
                    <th>Harga Beli</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($preview as $i => $row)
                <tr>
                    <td class="text-center text-muted">{{ $i + 1 }}</td>
                    <td class="font-weight-bold">{{ $row['nama'] }}</td>
                    <td>
                        <span class="badge badge-info">{{ $row['kode_kategori'] }}</span>
                        <small class="text-muted">{{ $row['kategori_nama'] }}</small>
                    </td>
                    <td>
                        @php
                            $statusClass = match($row['status']) {
                                'tersedia' => 'badge-success',
                                'rusak'    => 'badge-danger',
                                'servis'   => 'badge-info',
                                default    => 'badge-secondary'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst($row['status']) }}</span>
                    </td>
                    <td>{{ $row['merk'] ?? '—' }}</td>
                    <td><small>{{ $row['no_seri'] ?? '—' }}</small></td>
                    <td><small>{{ $row['lokasi'] ?? '—' }}</small></td>
                    <td><small>{{ $row['tgl_beli'] ?? '—' }}</small></td>
                    <td><small>{{ $row['harga_beli'] ? 'Rp '.number_format($row['harga_beli'],0,',','.') : '—' }}</small></td>
                    <td><small class="text-muted">{{ Str::limit($row['keterangan'] ?? '—', 40) }}</small></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>

{{-- ── Tombol Konfirmasi ──────────────────────────────── --}}
<div class="card">
    <div class="card-body">
        <div class="callout callout-warning mb-3">
            <i class="fas fa-info-circle mr-2"></i>
            Periksa kembali data di atas. Setelah klik <strong>"Simpan ke Database"</strong>,
            data akan langsung tersimpan dan tidak dapat dibatalkan secara massal.
            @if(count($errors) > 0)
                <br><strong>{{ count($errors) }} baris</strong> yang bermasalah akan <u>dilewati</u>.
            @endif
        </div>

        {{-- Re-upload file yang sama untuk konfirmasi simpan --}}
        <form action="{{ route('alat.import.save') }}" method="POST"
              enctype="multipart/form-data" id="saveForm">
            @csrf

            {{-- Hidden file re-upload prompt --}}
            <div class="d-flex align-items-center" style="gap:12px;flex-wrap:wrap">
                <div>
                    <label class="font-weight-bold d-block mb-1" style="font-size:13px">
                        Upload ulang file yang sama untuk konfirmasi:
                    </label>
                    <div class="input-group" style="max-width:340px">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="file_excel"
                                   id="confirmFile" accept=".xlsx,.xls" required>
                            <label class="custom-file-label" for="confirmFile">Pilih file Excel...</label>
                        </div>
                    </div>
                    <small class="text-muted">Pilih file Excel yang sama dengan yang di-preview</small>
                </div>

                <div class="mt-3" style="display:flex;gap:10px">
                    <button type="submit" class="btn btn-success" id="saveBtn">
                        <i class="fas fa-save mr-1"></i>
                        Simpan {{ count($preview) }} Alat ke Database
                    </button>
                    <a href="{{ route('alat.import.form') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Upload Ulang
                    </a>
                    <a href="{{ route('alat.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times mr-1"></i> Batalkan
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@else
{{-- Tidak ada data valid --}}
<div class="card">
    <div class="card-body text-center py-5">
        <i class="fas fa-file-excel fa-3x text-muted mb-3 d-block"></i>
        <h5 class="text-muted">Tidak ada data valid yang dapat disimpan.</h5>
        <p class="text-muted">Semua baris memiliki kesalahan. Perbaiki file Excel Anda dan upload ulang.</p>
        <a href="{{ route('alat.import.form') }}" class="btn btn-primary mt-2">
            <i class="fas fa-arrow-left mr-1"></i> Kembali & Upload Ulang
        </a>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
// Custom file label
$('#confirmFile').on('change', function() {
    const name = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').text(name || 'Pilih file Excel...');
});

// Konfirmasi sebelum simpan
$('#saveForm').on('submit', function(e) {
    e.preventDefault();
    const form = this;
    Swal.fire({
        title: 'Konfirmasi Simpan',
        html: `Anda akan menyimpan <strong>{{ count($preview) }} alat</strong> ke database.<br>Tindakan ini tidak dapat dibatalkan.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor:  '#6c757d',
        confirmButtonText:  '<i class="fas fa-save mr-1"></i> Ya, Simpan!',
        cancelButtonText:   'Periksa Lagi',
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('saveBtn').disabled = true;
            document.getElementById('saveBtn').innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
            form.submit();
        }
    });
});
</script>
@endpush
