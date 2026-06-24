{{-- resources/views/kalibrasi/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Kalibrasi')
@section('page-title', 'Detail Kalibrasi')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('kalibrasi.index') }}">Kalibrasi</a></li>
    <li class="breadcrumb-item active">{{ $kalibrasi->no_kalibrasi }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-7">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-ruler-combined mr-2"></i>{{ $kalibrasi->no_kalibrasi }}
                </h3>
                <div class="card-tools">{!! $kalibrasi->hasil_badge !!}</div>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted" width="42%">Alat</td>
                        <td>
                            <strong>{{ $kalibrasi->alat->nama }}</strong>
                            <code class="ml-1">{{ $kalibrasi->alat->kode }}</code>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kategori</td>
                        <td>{{ $kalibrasi->alat->kategori->nama }}</td>
                    </tr>
                    <tr><td colspan="2"><hr class="my-1"></td></tr>
                    <tr>
                        <td class="text-muted">Tgl Kalibrasi</td>
                        <td>{{ $kalibrasi->tgl_kalibrasi->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kalibrasi Berikutnya</td>
                        <td class="{{ $kalibrasi->isJatuhTempo() ? 'text-danger font-weight-bold' : '' }}">
                            {{ $kalibrasi->tgl_kalibrasi_berikutnya->format('d F Y') }}
                            @if($kalibrasi->isJatuhTempo())
                                @if($kalibrasi->tgl_kalibrasi_berikutnya < now())
                                    <span class="badge badge-danger ml-1">Terlambat!</span>
                                @else
                                    <span class="badge badge-warning ml-1">
                                        {{ now()->diffInDays($kalibrasi->tgl_kalibrasi_berikutnya) }} hari lagi
                                    </span>
                                @endif
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Hasil</td>
                        <td>{!! $kalibrasi->hasil_badge !!}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Lembaga Kalibrasi</td>
                        <td>{{ $kalibrasi->lembaga_kalibrasi ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">No. Sertifikat</td>
                        <td>{{ $kalibrasi->no_sertifikat ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Biaya</td>
                        <td>{{ $kalibrasi->biaya ? 'Rp '.number_format($kalibrasi->biaya,0,',','.') : '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dilakukan Oleh</td>
                        <td>{{ $kalibrasi->dilakukanOleh->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Keterangan</td>
                        <td>{{ $kalibrasi->keterangan ?: '—' }}</td>
                    </tr>
                    @if($kalibrasi->dokumen)
                    <tr>
                        <td class="text-muted">Dokumen</td>
                        <td>
                            <a href="{{ Storage::url($kalibrasi->dokumen) }}"
                               target="_blank" class="btn btn-xs btn-outline-info">
                                <i class="fas fa-file mr-1"></i>Lihat Dokumen
                            </a>
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
            @if(Auth::user()->canManage())
            <div class="card-footer d-flex" style="gap:8px">
                <a href="{{ route('kalibrasi.edit', $kalibrasi) }}"
                   class="btn btn-warning btn-sm">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <a href="{{ route('alat.show', $kalibrasi->alat) }}"
                   class="btn btn-info btn-sm">
                    <i class="fas fa-tools mr-1"></i> Lihat Alat
                </a>
                @if(Auth::user()->isAdmin())
                <form action="{{ route('kalibrasi.destroy', $kalibrasi) }}" method="POST"
                      class="ml-auto"
                      data-confirm="Hapus data kalibrasi {{ $kalibrasi->no_kalibrasi }}?">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                </form>
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- Riwayat kalibrasi alat yang sama --}}
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-2"></i>Riwayat Kalibrasi Alat Ini
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr><th>Tgl Kalibrasi</th><th>Hasil</th><th>Lembaga</th></tr>
                    </thead>
                    <tbody>
                        @foreach($kalibrasi->alat->kalibrasi->sortByDesc('tgl_kalibrasi') as $k)
                        <tr class="{{ $k->id === $kalibrasi->id ? 'table-active' : '' }}">
                            <td>{{ $k->tgl_kalibrasi->format('d/m/Y') }}</td>
                            <td>{!! $k->hasil_badge !!}</td>
                            <td><small>{{ $k->lembaga_kalibrasi ?: '—' }}</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('kalibrasi.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left mr-1"></i> Kembali
</a>
@endsection
