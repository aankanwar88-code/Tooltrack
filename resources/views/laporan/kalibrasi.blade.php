{{-- resources/views/laporan/kalibrasi.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Kalibrasi')
@section('page-title', 'Laporan Kalibrasi Alat')
@section('breadcrumb')
    <li class="breadcrumb-item">Laporan</li>
    <li class="breadcrumb-item active">Kalibrasi</li>
@endsection

@section('content')

<div class="card card-outline card-secondary">
    <div class="card-body">
        <form method="GET" class="form-inline flex-wrap" style="gap:.5rem">
            <div class="form-group">
                <label class="mr-1">Dari</label>
                <input type="date" name="tgl_dari" class="form-control form-control-sm"
                       value="{{ request('tgl_dari') }}">
            </div>
            <div class="form-group">
                <label class="mr-1">Sampai</label>
                <input type="date" name="tgl_sampai" class="form-control form-control-sm"
                       value="{{ request('tgl_sampai') }}">
            </div>
            <select name="hasil" class="form-control form-control-sm">
                <option value="">Semua Hasil</option>
                <option value="lulus"           {{ request('hasil')=='lulus'           ? 'selected':'' }}>Lulus</option>
                <option value="tidak_lulus"     {{ request('hasil')=='tidak_lulus'     ? 'selected':'' }}>Tidak Lulus</option>
                <option value="perlu_perbaikan" {{ request('hasil')=='perlu_perbaikan' ? 'selected':'' }}>Perlu Perbaikan</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
            <a href="{{ route('laporan.kalibrasi') }}" class="btn btn-sm btn-secondary"><i class="fas fa-redo"></i> Reset</a>
        </form>
    </div>
</div>

<div class="row mb-3">
    <div class="col-sm-3">
        <div class="info-box shadow-sm"><span class="info-box-icon bg-info"><i class="fas fa-list"></i></span>
            <div class="info-box-content"><span class="info-box-text">Total</span><span class="info-box-number">{{ $summary['total'] }}</span></div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="info-box shadow-sm"><span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
            <div class="info-box-content"><span class="info-box-text">Lulus</span><span class="info-box-number">{{ $summary['lulus'] }}</span></div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="info-box shadow-sm"><span class="info-box-icon bg-danger"><i class="fas fa-times"></i></span>
            <div class="info-box-content"><span class="info-box-text">Tidak Lulus</span><span class="info-box-number">{{ $summary['tidak_lulus'] }}</span></div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="info-box shadow-sm"><span class="info-box-icon bg-warning"><i class="fas fa-exclamation"></i></span>
            <div class="info-box-content"><span class="info-box-text">Total Biaya</span>
                <span class="info-box-number" style="font-size:18px">
                    Rp {{ number_format($summary['total_biaya'],0,',','.') }}
                </span>
            </div>
        </div>
    </div>
</div>

<div class="mb-3 d-flex" style="gap:.5rem">
    <a href="{{ route('laporan.kalibrasi.pdf', request()->query()) }}" class="btn btn-danger btn-sm" target="_blank">
        <i class="fas fa-file-pdf mr-1"></i> Export PDF
    </a>
    <a href="{{ route('laporan.kalibrasi.excel', request()->query()) }}" class="btn btn-success btn-sm">
        <i class="fas fa-file-excel mr-1"></i> Export Excel
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-bordered table-hover table-sm mb-0" data-table>
            <thead class="thead-light">
                <tr>
                    <th>No Kalibrasi</th><th>Alat</th><th>Tgl Kalibrasi</th>
                    <th>Tgl Berikutnya</th><th>Hasil</th><th>Lembaga</th>
                    <th>No Sertifikat</th><th>Biaya</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kalibrasi as $k)
                <tr class="{{ $k->isJatuhTempo() ? 'table-warning' : '' }}">
                    <td><code>{{ $k->no_kalibrasi }}</code></td>
                    <td><strong>{{ $k->alat->nama }}</strong><br>
                        <small class="text-muted">{{ $k->alat->kode }}</small></td>
                    <td>{{ $k->tgl_kalibrasi->format('d/m/Y') }}</td>
                    <td class="{{ $k->isJatuhTempo() ? 'text-danger font-weight-bold':'' }}">
                        {{ $k->tgl_kalibrasi_berikutnya->format('d/m/Y') }}
                    </td>
                    <td>{!! $k->hasil_badge !!}</td>
                    <td>{{ $k->lembaga_kalibrasi ?: '—' }}</td>
                    <td>{{ $k->no_sertifikat ?: '—' }}</td>
                    <td>{{ $k->biaya ? 'Rp '.number_format($k->biaya,0,',','.') : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
