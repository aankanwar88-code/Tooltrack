{{-- resources/views/laporan/alat.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Alat')
@section('page-title', 'Laporan Inventaris Alat')
@section('breadcrumb')
    <li class="breadcrumb-item">Laporan</li>
    <li class="breadcrumb-item active">Alat Kerja</li>
@endsection

@section('content')

<div class="card card-outline card-secondary">
    <div class="card-body">
        <form method="GET" class="form-inline flex-wrap" style="gap:.5rem">
            <select name="status" class="form-control form-control-sm">
                <option value="">Semua Status</option>
                @foreach(['tersedia','dipinjam','rusak','servis'] as $s)
                <option value="{{ $s }}" {{ request('status')==$s ? 'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <select name="kategori_id" class="form-control form-control-sm">
                <option value="">Semua Kategori</option>
                @foreach(\App\Models\KategoriAlat::orderBy('nama')->get() as $k)
                <option value="{{ $k->id }}" {{ request('kategori_id')==$k->id ? 'selected':'' }}>{{ $k->nama }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
            <a href="{{ route('laporan.alat') }}" class="btn btn-sm btn-secondary"><i class="fas fa-redo"></i> Reset</a>
        </form>
    </div>
</div>

<div class="row mb-3">
    @foreach([['Total','total','bg-info','fas fa-tools'],['Tersedia','tersedia','bg-success','fas fa-check'],['Dipinjam','dipinjam','bg-warning','fas fa-hand-holding'],['Rusak','rusak','bg-danger','fas fa-times'],['Servis','servis','bg-primary','fas fa-wrench']] as [$label,$key,$bg,$icon])
    <div class="col">
        <div class="info-box shadow-sm mb-2">
            <span class="info-box-icon {{ $bg }}"><i class="{{ $icon }}"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{ $label }}</span>
                <span class="info-box-number">{{ $summary[$key] }}</span>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="mb-3 d-flex" style="gap:.5rem">
    <a href="{{ route('laporan.alat.pdf', request()->query()) }}" class="btn btn-danger btn-sm" target="_blank">
        <i class="fas fa-file-pdf mr-1"></i> Export PDF
    </a>
    <a href="{{ route('laporan.alat.excel', request()->query()) }}" class="btn btn-success btn-sm">
        <i class="fas fa-file-excel mr-1"></i> Export Excel
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-bordered table-hover table-sm mb-0" data-table>
            <thead class="thead-light">
                <tr>
                    <th>Kode</th><th>Nama Alat</th><th>Kategori</th>
                    <th>Merk</th><th>Lokasi</th><th>Status</th>
                    <th>Tgl Beli</th><th>Harga Beli</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alat as $a)
                <tr>
                    <td><code>{{ $a->kode }}</code></td>
                    <td class="font-weight-bold">{{ $a->nama }}</td>
                    <td>{{ $a->kategori->nama }}</td>
                    <td>{{ $a->merk ?: '—' }}</td>
                    <td>{{ $a->lokasi ?: '—' }}</td>
                    <td>{!! $a->status_badge !!}</td>
                    <td>{{ $a->tgl_beli?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $a->harga_beli ? 'Rp '.number_format($a->harga_beli,0,',','.') : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
