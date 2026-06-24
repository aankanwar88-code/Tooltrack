{{-- resources/views/alat/qrcode-massal.blade.php --}}
@extends('layouts.app')

@section('title', 'Cetak QR Code Massal')
@section('page-title', 'Cetak QR Code Massal')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('alat.index') }}">Alat Kerja</a></li>
    <li class="breadcrumb-item active">QR Code Massal</li>
@endsection

@section('content')

<div class="mb-3 d-flex" style="gap:8px">
    <button onclick="window.print()" class="btn btn-primary btn-sm">
        <i class="fas fa-print mr-1"></i> Cetak Semua ({{ count($alatList) }} QR Code)
    </button>
    <a href="{{ route('alat.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

<div class="row" id="qrGrid">
    @foreach($alatList as $item)
    <div class="col-md-3 col-sm-4 col-6 mb-3">
        <div class="card text-center p-2 qr-card">
            <div class="qr-box">
                {!! $item['qr'] !!}
            </div>
            <div class="mt-2">
                <div class="font-weight-bold" style="font-size:11px;line-height:1.3">
                    {{ $item['alat']->nama }}
                </div>
                <code style="font-size:10px">{{ $item['alat']->kode }}</code>
                @if($item['alat']->lokasi)
                <div class="text-muted" style="font-size:10px">
                    {{ $item['alat']->lokasi }}
                </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

@endsection

@push('styles')
<style>
.qr-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    background: #fff;
}
.qr-box svg {
    width: 100% !important;
    height: auto !important;
}
@media print {
    .main-header, .main-sidebar, .content-header,
    .breadcrumb, .mb-3.d-flex { display: none !important; }
    .content-wrapper { margin: 0 !important; background: #fff !important; }
    .row { display: flex !important; flex-wrap: wrap !important; }
    .col-md-3 { width: 25% !important; }
    .qr-card { break-inside: avoid; border: 1px dashed #999 !important; }
    body { font-size: 10px; }
}
</style>
@endpush
