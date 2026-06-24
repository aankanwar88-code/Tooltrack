{{-- resources/views/peminjaman/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Peminjaman')
@section('page-title', 'Manajemen Peminjaman')
@section('breadcrumb')
    <li class="breadcrumb-item active">Peminjaman</li>
@endsection

@section('content')

<div class="card card-outline card-secondary">
    <div class="card-body">
        <form method="GET" class="form-inline flex-wrap" style="gap:.5rem">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Cari no. pinjam / alat / peminjam..."
                   value="{{ request('search') }}" style="min-width:240px">
            <select name="status" class="form-control form-control-sm">
                <option value="">Semua Status</option>
                <option value="menunggu"     {{ request('status')=='menunggu'     ? 'selected':'' }}>Menunggu</option>
                <option value="dipinjam"     {{ request('status')=='dipinjam'     ? 'selected':'' }}>Dipinjam</option>
                <option value="dikembalikan" {{ request('status')=='dikembalikan' ? 'selected':'' }}>Dikembalikan</option>
                <option value="ditolak"      {{ request('status')=='ditolak'      ? 'selected':'' }}>Ditolak</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="fas fa-search"></i> Filter
            </button>
            <a href="{{ route('peminjaman.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </a>
            <a href="{{ route('peminjaman.create') }}" class="btn btn-sm btn-success ml-auto">
                <i class="fas fa-plus"></i> Ajukan Peminjaman
            </a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-bordered table-hover table-sm mb-0">
            <thead class="thead-light">
                <tr>
                    <th>No Pinjam</th>
                    @if(Auth::user()->canManage()) <th>Peminjam</th> @endif
                    <th>Jumlah Alat</th>
                    <th>Alat Dipinjam</th>
                    <th>Tgl Pinjam</th>
                    <th>Rencana Kembali</th>
                    <th>Status</th>
                    <th width="110">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peminjaman as $p)
                <tr class="{{ $p->isTerlambat() ? 'table-warning' : '' }}">
                    <td><code>{{ $p->no_pinjam }}</code></td>
                    @if(Auth::user()->canManage())
                    <td>
                        <div class="font-weight-bold">{{ $p->peminjam->name }}</div>
                        <small class="text-muted">{{ $p->peminjam->departemen ?? '' }}</small>
                    </td>
                    @endif
                    <td class="text-center">
                        <span class="badge badge-info">{{ $p->jumlahAlat() }} alat</span>
                    </td>
                    <td>
                        @foreach($p->detail->take(2) as $d)
                        <div style="font-size:11px">
                            • {{ $d->alat->nama }}
                            @if($d->sudahDikembalikan())
                                <span class="badge badge-success" style="font-size:9px">Kembali</span>
                            @endif
                        </div>
                        @endforeach
                        @if($p->detail->count() > 2)
                        <small class="text-muted">+{{ $p->detail->count() - 2 }} lainnya...</small>
                        @endif
                    </td>
                    <td>{{ $p->tgl_pinjam->format('d/m/Y') }}</td>
                    <td class="{{ $p->isTerlambat() ? 'text-danger font-weight-bold' : '' }}">
                        {{ $p->tgl_kembali_rencana->format('d/m/Y') }}
                        @if($p->isTerlambat())
                        <br><small><i class="fas fa-exclamation-circle"></i>
                            {{ $p->tgl_kembali_rencana->diffInDays(now()) }}h terlambat
                        </small>
                        @endif
                    </td>
                    <td>{!! $p->status_badge !!}</td>
                    <td>
                        <div class="d-flex" style="gap:4px">
                            <a href="{{ route('peminjaman.show', $p) }}"
                               class="btn btn-xs btn-info" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($p->status === 'dipinjam' && Auth::user()->canManage())
                            <a href="{{ route('peminjaman.show', $p) }}"
                               class="btn btn-xs btn-success" title="Proses Pengembalian">
                                <i class="fas fa-undo"></i> Kembali
                            </a>
                            @endif
                            @if($p->status === 'menunggu' && Auth::user()->canManage())
                            <form action="{{ route('peminjaman.approve', $p) }}"
                                  method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-xs btn-primary" title="Setujui">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ Auth::user()->canManage() ? 8 : 7 }}"
                        class="text-center text-muted py-4">
                        <i class="fas fa-clipboard-list fa-2x d-block mb-2"></i>
                        Belum ada data peminjaman.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $peminjaman->links() }}
        <span class="float-right text-muted small">Total: {{ $peminjaman->total() }}</span>
    </div>
</div>

@endsection