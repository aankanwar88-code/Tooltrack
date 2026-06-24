{{-- resources/views/laporan/peminjaman.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Peminjaman')
@section('page-title', 'Laporan Peminjaman')
@section('breadcrumb')
    <li class="breadcrumb-item">Laporan</li>
    <li class="breadcrumb-item active">Peminjaman</li>
@endsection

@section('content')

{{-- Filter --}}
<div class="card card-outline card-secondary">
    <div class="card-header"><h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filter Laporan</h3></div>
    <div class="card-body">
        <form method="GET" class="form-inline flex-wrap" style="gap:.5rem" id="filterForm">
            <div class="form-group">
                <label class="mr-1">Dari</label>
                <input type="date" name="tgl_dari" class="form-control form-control-sm" value="{{ request('tgl_dari') }}">
            </div>
            <div class="form-group">
                <label class="mr-1">Sampai</label>
                <input type="date" name="tgl_sampai" class="form-control form-control-sm" value="{{ request('tgl_sampai') }}">
            </div>
            <select name="status" class="form-control form-control-sm">
                <option value="">Semua Status</option>
                <option value="menunggu"     {{ request('status')=='menunggu'     ? 'selected':'' }}>Menunggu</option>
                <option value="dipinjam"     {{ request('status')=='dipinjam'     ? 'selected':'' }}>Dipinjam</option>
                <option value="dikembalikan" {{ request('status')=='dikembalikan' ? 'selected':'' }}>Dikembalikan</option>
                <option value="ditolak"      {{ request('status')=='ditolak'      ? 'selected':'' }}>Ditolak</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
            <a href="{{ route('laporan.peminjaman') }}" class="btn btn-sm btn-secondary"><i class="fas fa-redo"></i> Reset</a>
        </form>
    </div>
</div>

{{-- Ringkasan --}}
<div class="row mb-3">
    <div class="col-sm-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-info"><i class="fas fa-list"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total</span>
                <span class="info-box-number">{{ $summary['total'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-warning"><i class="fas fa-hand-holding"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Dipinjam</span>
                <span class="info-box-number">{{ $summary['dipinjam'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Dikembalikan</span>
                <span class="info-box-number">{{ $summary['dikembalikan'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Terlambat</span>
                <span class="info-box-number">{{ $summary['terlambat'] }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Tombol Export --}}
<div class="mb-3 d-flex" style="gap:.5rem">
    <a href="{{ route('laporan.peminjaman.pdf', request()->query()) }}" class="btn btn-danger btn-sm" target="_blank">
        <i class="fas fa-file-pdf mr-1"></i> Export PDF
    </a>
    <a href="{{ route('laporan.peminjaman.excel', request()->query()) }}" class="btn btn-success btn-sm">
        <i class="fas fa-file-excel mr-1"></i> Export Excel
    </a>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-body p-0">
        <table class="table table-bordered table-hover table-sm mb-0" data-table>
            <thead class="thead-light">
                <tr>
                    <th>No Pinjam</th>
                    <th>Alat</th>
                    <th>Peminjam</th>
                    <th>Departemen</th>
                    <th>Tgl Pinjam</th>
                    <th>Tgl Kembali</th>
                    <th>Tgl Kembali Aktual</th>
                    <th>Status</th>
                    <th>Keterlambatan</th>
                    <th>Kondisi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peminjaman as $p)
			<tr class="{{ $p->isTerlambat() ? 'table-warning' : '' }}">
    				<td><code style="font-size:11px">{{ $p->no_pinjam }}</code></td>
    				<td>
        				{{-- Tampilkan semua alat dalam satu transaksi --}}
        				@foreach($p->detail as $d)
            				<div style="font-size:12px">
                					• {{ $d->alat->nama ?? '—' }}
                					<small class="text-muted">({{ $d->alat->kode ?? '' }})</small>
            				</div>
        				@endforeach
    				</td>
    				<td>{{ $p->peminjam->name }}</td>
    				<td>{{ $p->peminjam->departemen ?? '—' }}</td>
    				<td>{{ $p->tgl_pinjam->format('d/m/Y') }}</td>
    				<td class="{{ $p->isTerlambat() ? 'text-danger font-weight-bold' : '' }}">
        				{{ $p->tgl_kembali_rencana->format('d/m/Y') }}
    				</td>
    				<td>
        				{{-- Ambil tgl kembali aktual dari detail --}}
        				@php
            				$tglKembali = $p->detail->whereNotNull('tgl_kembali_aktual')->max('tgl_kembali_aktual');
        				@endphp
        				{{ $tglKembali ? \Carbon\Carbon::parse($tglKembali)->format('d/m/Y') : '—' }}
    				</td>
    				<td>{!! $p->status_badge !!}</td>
    				<td>
        			@if($p->isTerlambat())
            			<span class="badge badge-danger">
                				{{ $p->tgl_kembali_rencana->diffInDays(now()) }} hari
            			</span>
        			@elseif($p->status === 'dikembalikan')
           			@php
                				$tglRencana  = $p->tgl_kembali_rencana;
                				$tglAktual   = $p->detail->whereNotNull('tgl_kembali_aktual')->max('tgl_kembali_aktual');
            			@endphp
            			@if($tglAktual && \Carbon\Carbon::parse($tglAktual)->gt($tglRencana))
                				<span class="badge badge-warning">
                    				{{ $tglRencana->diffInDays(\Carbon\Carbon::parse($tglAktual)) }} hari
                				</span>
            			@else
                				<span class="text-muted">—</span>
            			@endif
       		 	@else
            			<span class="text-muted">—</span>
        			@endif
    			</td>
    			<td>
        			<small class="text-muted">
            			{{-- Kondisi kembali dari detail --}}
            			@php
                				$kondisi = $p->detail->whereNotNull('kondisi_kembali')->pluck('kondisi_kembali')->first();
            			@endphp
            			{{ $kondisi ? \Str::limit($kondisi, 30) : '—' }}
        			</small>
    			</td>
		</tr>
		@empty
		<tr>
    			<td colspan="10" class="text-center text-muted py-5">
        			<i class="fas fa-search fa-2x d-block mb-2"></i>
        			Tidak ada data untuk filter ini.
    			</td>
		</tr>
		@endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
