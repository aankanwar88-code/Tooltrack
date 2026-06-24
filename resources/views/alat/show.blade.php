{{-- resources/views/alat/show.blade.php --}}
@extends('layouts.app')

@section('title', $alat->nama)
@section('page-title', 'Detail Alat Kerja')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('alat.index') }}">Alat Kerja</a></li>
    <li class="breadcrumb-item active">{{ $alat->kode }}</li>
@endsection

@section('content')
<div class="row">

    {{-- Info Utama --}}
    <div class="col-md-5">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tools mr-2"></i>{{ $alat->nama }}</h3>
                <div class="card-tools">{!! $alat->status_badge !!}</div>
            </div>
            <div class="card-body">
                @if($alat->foto)
                <div class="text-center mb-3">
                    <img src="{{ Storage::url($alat->foto) }}" alt="{{ $alat->nama }}"
                         class="img-fluid rounded" style="max-height:200px">
                </div>
                @endif

                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted" width="40%">Kode</td>
                        <td><code class="font-weight-bold">{{ $alat->kode }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kategori</td>
                        <td>{{ $alat->kategori->nama }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Merk</td>
                        <td>{{ $alat->merk ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">No. Seri</td>
                        <td>{{ $alat->no_seri ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Lokasi</td>
                        <td>{{ $alat->lokasi ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tgl Beli</td>
                        <td>{{ $alat->tgl_beli?->format('d F Y') ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Harga Beli</td>
                        <td>{{ $alat->harga_beli ? 'Rp '.number_format($alat->harga_beli,0,',','.') : '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Keterangan</td>
                        <td>{{ $alat->keterangan ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Ditambahkan</td>
                        <td>{{ $alat->created_at->format('d F Y') }}</td>
                    </tr>
                </table>
            </div>
        @if(Auth::user()->canManage())
        <div class="card-footer d-flex" style="gap:8px">
            <a href="{{ route('qrcode.show', $alat) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-qrcode mr-1"></i> QR Code
        </a>
        <a href="{{ route('alat.edit', $alat) }}" class="btn btn-warning btn-sm">
            <i class="fas fa-edit mr-1"></i> Edit
        </a>
        <a href="{{ route('kalibrasi.create') }}?alat_id={{ $alat->id }}"
           class="btn btn-info btn-sm">
            <i class="fas fa-ruler-combined mr-1"></i> Tambah Kalibrasi
        </a>
    </div>
    @endif
        </div>
    </div>

    <div class="col-md-7">

        {{-- Kalibrasi terakhir --}}
        @php $kalTerakhir = $alat->kalibrasiTerakhir(); @endphp
        <div class="card card-outline {{ $alat->isKalibrasiJatuhTempo() ? 'card-warning' : 'card-info' }}">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-ruler-combined mr-2"></i>Status Kalibrasi
                </h3>
            </div>
            <div class="card-body">
                @if($kalTerakhir)
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" width="45%">No. Kalibrasi</td>
                        <td><code>{{ $kalTerakhir->no_kalibrasi }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tgl Kalibrasi</td>
                        <td>{{ $kalTerakhir->tgl_kalibrasi->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kalibrasi Berikutnya</td>
                        <td class="{{ $alat->isKalibrasiJatuhTempo() ? 'text-danger font-weight-bold' : '' }}">
                            {{ $kalTerakhir->tgl_kalibrasi_berikutnya->format('d F Y') }}
                            @if($kalTerakhir->tgl_kalibrasi_berikutnya < now())
                                <span class="badge badge-danger ml-1">Terlambat!</span>
                            @elseif($alat->isKalibrasiJatuhTempo())
                                <span class="badge badge-warning ml-1">
                                    {{ now()->diffInDays($kalTerakhir->tgl_kalibrasi_berikutnya) }} hari lagi
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Hasil</td>
                        <td>{!! $kalTerakhir->hasil_badge !!}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Lembaga</td>
                        <td>{{ $kalTerakhir->lembaga_kalibrasi ?: '—' }}</td>
                    </tr>
                </table>
                @else
                <p class="text-muted text-center py-2 mb-0">
                    <i class="fas fa-info-circle mr-1"></i>Belum pernah dikalibrasi.
                </p>
                @endif
            </div>
        </div>

        {{-- Riwayat peminjaman --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-2"></i>Riwayat Peminjaman
                    <span class="badge badge-secondary ml-1">{{ $alat->peminjaman->count() }}</span>
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Peminjam</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alat->peminjaman->take(5) as $p)
                        <tr>
                            <td>{{ $p->peminjam->name }}</td>
                            <td>{{ $p->tgl_pinjam->format('d/m/Y') }}</td>
                            <td>{{ $p->tgl_kembali_aktual?->format('d/m/Y') ?? $p->tgl_kembali_rencana->format('d/m/Y') }}</td>
                            <td>{!! $p->status_badge !!}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">
                                Belum pernah dipinjam.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<a href="{{ route('alat.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left mr-1"></i> Kembali
</a>
<a href="{{ route('qrcode.show', $alat) }}" class="btn btn-secondary btn-sm">
    <i class="fas fa-qrcode mr-1"></i> QR Code
</a>

<a href="{{ route('alat.edit', $alat) }}" class="btn btn-warning btn-sm">
    <i class="fas fa-edit mr-1"></i> Edit
</a>
<a href="{{ route('alat.create') }}" class="btn btn-sm btn-success ml-auto">
    <i class="fas fa-plus mr-1"></i> Tambah Alat
</a>

<button type="button" 
        class="btn btn-sm btn-secondary" 
        id="btnQrMassal" 
        style="display:none" 
        onclick="cetakQrMassal()">
    <i class="fas fa-qrcode mr-1"></i> 
    QR Massal (<span id="qrCount">0</span>)
</button>
@endsection
