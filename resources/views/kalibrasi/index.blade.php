{{-- resources/views/kalibrasi/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Kalibrasi Alat')
@section('page-title', 'Manajemen Kalibrasi')
@section('breadcrumb')
    <li class="breadcrumb-item active">Kalibrasi</li>
@endsection

@section('content')

@if($alatJatuhTempo > 0)
<div class="alert alert-warning alert-dismissible">
    <i class="fas fa-bell mr-2"></i>
    <strong>Perhatian!</strong> Ada <strong>{{ $alatJatuhTempo }}</strong> alat yang jadwal kalibrasinya jatuh tempo dalam 30 hari ke depan.
    <a href="{{ route('kalibrasi.index', ['jatuh_tempo'=>1]) }}" class="btn btn-sm btn-warning ml-2">Lihat</a>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif

{{-- Filter --}}
<div class="card card-outline card-secondary">
    <div class="card-body">
        <form method="GET" class="form-inline flex-wrap" style="gap:.5rem">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Cari no. kalibrasi / alat..." value="{{ request('search') }}" style="min-width:220px">
            <select name="hasil" class="form-control form-control-sm">
                <option value="">Semua Hasil</option>
                <option value="lulus"           {{ request('hasil')=='lulus'           ? 'selected':'' }}>Lulus</option>
                <option value="tidak_lulus"     {{ request('hasil')=='tidak_lulus'     ? 'selected':'' }}>Tidak Lulus</option>
                <option value="perlu_perbaikan" {{ request('hasil')=='perlu_perbaikan' ? 'selected':'' }}>Perlu Perbaikan</option>
            </select>
            <label class="mr-1 ml-2">
                <input type="checkbox" name="jatuh_tempo" value="1" {{ request('jatuh_tempo') ? 'checked':'' }}> Jatuh Tempo
            </label>
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Filter</button>
            <a href="{{ route('kalibrasi.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-redo"></i> Reset</a>

            @if(Auth::user()->canManage())
            <a href="{{ route('kalibrasi.create') }}" class="btn btn-sm btn-success ml-auto">
                <i class="fas fa-plus"></i> Tambah Kalibrasi
            </a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-bordered table-hover table-sm mb-0">
            <thead class="thead-light">
                <tr>
                    <th>No Kalibrasi</th>
                    <th>Alat</th>
                    <th>Tgl Kalibrasi</th>
                    <th>Tgl Berikutnya</th>
                    <th>Lembaga</th>
                    <th>Hasil</th>
                    <th>Biaya</th>
                    <th width="100">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kalibrasi as $k)
                <tr class="{{ $k->isJatuhTempo() ? 'table-warning' : '' }}">
                    <td><code>{{ $k->no_kalibrasi }}</code></td>
                    <td class="font-weight-bold">{{ $k->alat->nama }}<br>
                        <small class="text-muted">{{ $k->alat->kode }}</small>
                    </td>
                    <td>{{ $k->tgl_kalibrasi->format('d/m/Y') }}</td>
                    <td class="{{ $k->isJatuhTempo() ? 'text-danger font-weight-bold' : '' }}">
                        {{ $k->tgl_kalibrasi_berikutnya->format('d/m/Y') }}
                        @if($k->isJatuhTempo())
                            <br><small><i class="fas fa-exclamation-circle"></i>
                                {{ $k->tgl_kalibrasi_berikutnya < now()->toDateString() ? 'Terlambat!' : now()->diffInDays($k->tgl_kalibrasi_berikutnya).' hari lagi' }}
                            </small>
                        @endif
                    </td>
                    <td>{{ $k->lembaga_kalibrasi ?: '—' }}</td>
                    <td>{!! $k->hasil_badge !!}</td>
                    <td>{{ $k->biaya ? 'Rp '.number_format($k->biaya,0,',','.') : '—' }}</td>
                    <td>
                        <a href="{{ route('kalibrasi.show', $k) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                        @if(Auth::user()->canManage())
                        <a href="{{ route('kalibrasi.edit', $k) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                        @endif
                        @if(Auth::user()->isAdmin())
                        <form action="{{ route('kalibrasi.destroy', $k) }}" method="POST" class="d-inline"
                              data-confirm="Hapus data kalibrasi ini?">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="fas fa-ruler-combined fa-2x d-block mb-2"></i>Belum ada data kalibrasi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $kalibrasi->links() }}
    </div>
</div>
@endsection
