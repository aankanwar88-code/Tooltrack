{{-- resources/views/alat/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Alat Kerja')
@section('page-title', 'Manajemen Alat Kerja')
@section('breadcrumb')
    <li class="breadcrumb-item active">Alat Kerja</li>
<form id="formQrMassal" action="{{ route('qrcode.massal') }}" method="POST" target="_blank">
    @csrf
    <div id="qrInputs"></div>
</form>

@endsection

@section('content')

{{-- Filter --}}
<div class="card card-outline card-secondary">
    <div class="card-body">
        <form method="GET" action="{{ route('alat.index') }}" class="form-inline flex-wrap" style="gap:.5rem">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Cari nama / kode / merk..." value="{{ request('search') }}" style="min-width:220px">
            <select name="status" class="form-control form-control-sm">
                <option value="">Semua Status</option>
                <option value="tersedia"  {{ request('status')=='tersedia'  ? 'selected':'' }}>Tersedia</option>
                <option value="dipinjam"  {{ request('status')=='dipinjam'  ? 'selected':'' }}>Dipinjam</option>
                <option value="rusak"     {{ request('status')=='rusak'     ? 'selected':'' }}>Rusak</option>
                <option value="servis"    {{ request('status')=='servis'    ? 'selected':'' }}>Servis</option>
            </select>
            <select name="kategori_id" class="form-control form-control-sm">
                <option value="">Semua Kategori</option>
                @foreach($kategori as $k)
                    <option value="{{ $k->id }}" {{ request('kategori_id')==$k->id ? 'selected':'' }}>{{ $k->nama }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Filter</button>
            <a href="{{ route('alat.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-redo"></i> Reset</a>

            @if(Auth::user()->canManage())
            <button type="button" class="btn btn-sm btn-secondary" id="btnQrMassal" style="display:none" onclick="cetakQrMassal()">
                    <i class="fas fa-qrcode mr-1"></i> QR Massal (<span id="qrCount">0</span>)
                </button>
                <a href="{{ route('alat.create') }}" class="btn btn-sm btn-success ml-auto">
                <i class="fas fa-plus"></i> Tambah Alat
            </a>
            @endif
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-body p-0">
        <table class="table table-bordered table-hover table-sm mb-0">
            <thead class="thead-light">
                <tr>
                    <th width="30"><input type="checkbox" id="checkAll" title="Pilih semua"></th>
                    <th width="80">Kode</th>
                    <th>Nama Alat</th>
                    <th>Kategori</th>
                    <th>Merk</th>
                    <th>Lokasi</th>
                    <th width="100">Status</th>
                    <th>Kalibrasi Berikutnya</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alat as $a)
                <tr>
                    <td><input type="checkbox" class="qr-check" value="{{ $a->id }}"></td>
                    <td><code>{{ $a->kode }}</code></td>
                    <td class="font-weight-bold">{{ $a->nama }}</td>
                    <td>{{ $a->kategori->nama }}</td>
                    <td>{{ $a->merk ?: '—' }}</td>
                    <td>{{ $a->lokasi ?: '—' }}</td>
                    <td>{!! $a->status_badge !!}</td>
                    <td>
                        @php $kal = $a->kalibrasiTerakhir() @endphp
                        @if($kal)
                            <span class="{{ $kal->isJatuhTempo() ? 'text-danger font-weight-bold' : '' }}">
                                {{ $kal->tgl_kalibrasi_berikutnya->format('d/m/Y') }}
                                @if($kal->isJatuhTempo()) <i class="fas fa-exclamation-circle"></i> @endif
                            </span>
                        @else
                            <span class="text-muted">Belum dikalibrasi</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('alat.show', $a) }}" class="btn btn-xs btn-info" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if(Auth::user()->canManage())
                        <a href="{{ route('alat.edit', $a) }}" class="btn btn-xs btn-warning" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('alat.destroy', $a) }}" method="POST" class="d-inline"
                              data-confirm="Hapus alat {{ $a->nama }}?">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-danger" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="fas fa-tools fa-2x mb-2 d-block"></i>
                        Tidak ada alat ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $alat->links() }}
        <span class="float-right text-muted small">Total: {{ $alat->total() }} alat</span>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Checkbox select all
$('#checkAll').on('change', function () {
    $('.qr-check').prop('checked', this.checked).trigger('change');
});

// Update counter QR button
$(document).on('change', '.qr-check', function () {
    const count = $('.qr-check:checked').length;
    $('#qrCount').text(count);
    $('#btnQrMassal').toggle(count > 0);
});

// Submit form QR massal
function cetakQrMassal() {
    const ids = $('.qr-check:checked').map(function() { return this.value; }).get();
    if (!ids.length) return;
    const form = $('#formQrMassal');
    $('#qrInputs').empty();
    ids.forEach(id => {
        $('#qrInputs').append('<input type="hidden" name="alat_ids[]" value="' + id + '">');
    });
    form.submit();
}
</script>
@endpush
