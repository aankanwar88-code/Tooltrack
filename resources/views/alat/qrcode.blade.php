{{-- resources/views/alat/qrcode.blade.php --}}
@extends('layouts.app')

@section('title', 'QR Code — ' . $alat->nama)
@section('page-title', 'QR Code Alat')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('alat.index') }}">Alat Kerja</a></li>
    <li class="breadcrumb-item"><a href="{{ route('alat.show', $alat) }}">{{ $alat->kode }}</a></li>
    <li class="breadcrumb-item active">QR Code</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-md-6">

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-qrcode mr-2"></i>QR Code — {{ $alat->nama }}
        </h3>
    </div>
    <div class="card-body text-center">

        {{-- QR Code --}}
        <div class="qr-wrapper d-inline-block p-3 bg-white border rounded mb-3"
             style="box-shadow:0 2px 12px rgba(0,0,0,.08)">
            {!! $qr !!}
        </div>

        {{-- Info alat --}}
        <div class="mb-3">
            <div class="font-weight-bold" style="font-size:16px">{{ $alat->nama }}</div>
            <code style="font-size:13px">{{ $alat->kode }}</code>
            <div class="text-muted small mt-1">{{ $alat->kategori->nama }}</div>
            @if($alat->lokasi)
            <div class="text-muted small">
                <i class="fas fa-map-marker-alt mr-1"></i>{{ $alat->lokasi }}
            </div>
            @endif
        </div>

        {{-- URL --}}
        <div class="bg-light rounded p-2 mb-3" style="font-size:11px;word-break:break-all;color:#6b7280">
            <i class="fas fa-link mr-1"></i>{{ $url }}
        </div>

        {{-- Tombol aksi --}}
        <div class="d-flex justify-content-center flex-wrap" style="gap:8px">
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                <i class="fas fa-print mr-1"></i> Cetak QR Code
            </button>
            <a href="{{ route('qrcode.download', $alat) }}" class="btn btn-success btn-sm">
                <i class="fas fa-download mr-1"></i> Download SVG
            </a>
            <a href="{{ route('alat.show', $alat) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>

    </div>
</div>

{{-- Panduan penggunaan --}}
<div class="card card-outline card-info mt-0">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Cara Penggunaan</h3>
    </div>
    <div class="card-body" style="font-size:13px">
        <ol class="pl-3 mb-0">
            <li class="mb-2">Klik <strong>"Cetak QR Code"</strong> atau <strong>"Download SVG"</strong>.</li>
            <li class="mb-2">Cetak dan tempel QR Code di badan alat atau tempat penyimpanan alat.</li>
            <li class="mb-2">Scan QR Code menggunakan kamera HP — akan langsung membuka halaman detail alat di browser.</li>
            <li>Halaman detail menampilkan: status alat, riwayat peminjaman, dan jadwal kalibrasi.</li>
        </ol>
    </div>
</div>

</div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .main-header, .main-sidebar, .content-header,
    .breadcrumb, .card-outline.card-info,
    .card-footer, .btn, .card-header { display: none !important; }
    .content-wrapper { margin: 0 !important; background: #fff !important; }
    .card { border: none !important; box-shadow: none !important; }
    .qr-wrapper { box-shadow: none !important; }

    .print-label {
        display: block !important;
        font-family: Arial, sans-serif;
        text-align: center;
        margin-top: 8px;
    }
}
.print-label { display: none; }
</style>
@endpush
