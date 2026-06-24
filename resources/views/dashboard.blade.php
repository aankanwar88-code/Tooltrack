{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

{{-- ── Kartu Statistik ─────────────────────────────── --}}
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['alat_tersedia'] }}</h3>
                <p>Alat Tersedia</p>
            </div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <a href="{{ route('alat.index', ['status' => 'tersedia']) }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['alat_dipinjam'] }}</h3>
                <p>Sedang Dipinjam</p>
            </div>
            <div class="icon"><i class="fas fa-hand-holding"></i></div>
            <a href="{{ route('peminjaman.index', ['status' => 'dipinjam']) }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['total_alat'] }}</h3>
                <p>Total Inventaris</p>
            </div>
            <div class="icon"><i class="fas fa-tools"></i></div>
            <a href="{{ route('alat.index') }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['alat_rusak_servis'] }}</h3>
                <p>Rusak / Servis</p>
            </div>
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            <a href="{{ route('alat.index', ['status' => 'rusak']) }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="row">
    {{-- ── Peminjaman Terbaru ─────────────────────── --}}
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-clipboard-list mr-2"></i>Peminjaman Terbaru</h3>
                <div class="card-tools">
                    <a href="{{ route('peminjaman.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-list"></i> Semua
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>No Pinjam</th>
                            <th>Alat</th>
                            <th>Peminjam</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($peminjamanTerbaru as $p)
                        <tr>
                            <td><code>{{ $p->no_pinjam }}</code></td>
                            <td>{{ $p->daftarAlat() }}</td>
                            <td>{{ $p->peminjam->name }}</td>
                            <td class="{{ $p->isTerlambat() ? 'text-danger font-weight-bold' : '' }}">
                                {{ $p->tgl_kembali_rencana->format('d/m/Y') }}
                                @if($p->isTerlambat()) <i class="fas fa-exclamation-circle"></i> @endif
                            </td>
                            <td>{!! $p->status_badge !!}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data peminjaman</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Alert & Info ──────────────────────────── --}}
    <div class="col-md-4">

        {{-- Kalibrasi jatuh tempo --}}
        @if($kalibrasiJatuhTempo->count())
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bell mr-2"></i>Kalibrasi Jatuh Tempo</h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($kalibrasiJatuhTempo as $k)
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <div>
                            <div class="font-weight-bold text-sm">{{ $k->alat->nama }}</div>
                            <small class="text-muted">{{ $k->tgl_kalibrasi_berikutnya->format('d M Y') }}</small>
                        </div>
                        @if($k->tgl_kalibrasi_berikutnya < now()->toDateString())
                            <span class="badge badge-danger">Terlambat</span>
                        @else
                            <span class="badge badge-warning">
                                {{ now()->diffInDays($k->tgl_kalibrasi_berikutnya) }}h lagi
                            </span>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="card-footer p-2">
                <a href="{{ route('kalibrasi.index', ['jatuh_tempo'=>1]) }}" class="btn btn-sm btn-warning btn-block">
                    Lihat Semua
                </a>
            </div>
        </div>
        @endif

        {{-- Menunggu persetujuan --}}
        @if($menungguPersetujuan->count() && Auth::user()->canManage())
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-hourglass-half mr-2"></i>Menunggu Persetujuan</h3>
                <span class="badge badge-danger ml-2">{{ $menungguPersetujuan->count() }}</span>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($menungguPersetujuan as $p)
                    <li class="list-group-item py-2">
                        <div class="font-weight-bold text-sm">{{ $p->daftarAlat() }}</div>
                        <small class="text-muted">{{ $p->peminjam->name }} — {{ $p->tgl_pinjam->format('d M Y') }}</small>
                        <div class="mt-1">
                            <a href="{{ route('peminjaman.show', $p) }}" class="btn btn-xs btn-primary">Proses</a>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- ── Peminjaman Terlambat ──────────────────────────── --}}
@if($peminjamanTerlambat->count())
<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-exclamation-triangle mr-2 text-danger"></i>
                    Peminjaman Terlambat Dikembalikan
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>No Pinjam</th><th>Alat</th><th>Peminjam</th>
                            <th>Tgl Kembali</th><th>Keterlambatan</th><th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($peminjamanTerlambat as $p)
                        <tr>
                            <td><code>{{ $p->no_pinjam }}</code></td>
                            <td>{{ $p->daftarAlat() }}</td>
                            <td>{{ $p->peminjam->name }}</td>
                            <td class="text-danger font-weight-bold">{{ $p->tgl_kembali_rencana->format('d/m/Y') }}</td>
                            <td><span class="badge badge-danger">{{ $p->tgl_kembali_rencana->diffInDays(now()) }} hari</span></td>
                            <td>
                                <a href="{{ route('peminjaman.show', $p) }}" class="btn btn-xs btn-warning">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
