{{-- resources/views/activity-log/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Activity Log')
@section('page-title', 'History Log Aktivitas')
@section('breadcrumb')
    <li class="breadcrumb-item active">Activity Log</li>
@endsection

@section('content')

{{-- Filter --}}
<div class="card card-outline card-secondary">
    <div class="card-body">
        <form method="GET" class="form-inline flex-wrap" style="gap:.5rem">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Cari deskripsi..." value="{{ request('search') }}" style="min-width:220px">

            <select name="user_id" class="form-control form-control-sm">
                <option value="">Semua User</option>
                @foreach($users as $u)
                <option value="{{ $u->id }}" {{ request('user_id')==$u->id ? 'selected':'' }}>
                    {{ $u->name }}
                </option>
                @endforeach
            </select>

            <select name="action" class="form-control form-control-sm">
                <option value="">Semua Aksi</option>
                @foreach(['created'=>'Tambah','updated'=>'Edit','deleted'=>'Hapus','dipinjam'=>'Pinjam','dikembalikan'=>'Kembali','ditolak'=>'Tolak','kalibrasi'=>'Kalibrasi','login'=>'Login','logout'=>'Logout'] as $val => $label)
                <option value="{{ $val }}" {{ request('action')==$val ? 'selected':'' }}>{{ $label }}</option>
                @endforeach
            </select>

            <select name="model_type" class="form-control form-control-sm">
                <option value="">Semua Modul</option>
                @foreach(['Alat'=>'Alat','Peminjaman'=>'Peminjaman','Kalibrasi'=>'Kalibrasi','User'=>'User'] as $val => $label)
                <option value="{{ $val }}" {{ request('model_type')==$val ? 'selected':'' }}>{{ $label }}</option>
                @endforeach
            </select>

            <input type="date" name="tgl_dari" class="form-control form-control-sm"
                   value="{{ request('tgl_dari') }}" title="Dari tanggal">
            <input type="date" name="tgl_sampai" class="form-control form-control-sm"
                   value="{{ request('tgl_sampai') }}" title="Sampai tanggal">

            <button type="submit" class="btn btn-sm btn-primary">
                <i class="fas fa-search"></i> Filter
            </button>
            <a href="{{ route('activity-log.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">
            <i class="fas fa-history mr-2"></i>
            {{ $logs->total() }} Log Ditemukan
        </h3>
        <form action="{{ route('activity-log.hapus-semua') }}" method="POST"
              data-confirm="Hapus log lebih dari 30 hari lalu? Tindakan ini tidak dapat dibatalkan.">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-xs btn-outline-danger">
                <i class="fas fa-trash mr-1"></i> Hapus Log Lama
            </button>
        </form>
    </div>

    <div class="card-body p-0">
        <table class="table table-sm table-hover mb-0">
            <thead class="thead-light">
                <tr>
                    <th width="140">Waktu</th>
                    <th width="130">User</th>
                    <th width="80">Aksi</th>
                    <th width="90">Modul</th>
                    <th>Deskripsi</th>
                    <th width="80">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td style="font-size:11px;white-space:nowrap">
                        <div>{{ $log->created_at->format('d/m/Y') }}</div>
                        <div class="text-muted">{{ $log->created_at->format('H:i:s') }}</div>
                    </td>
                    <td>
                        <div style="font-size:12px;font-weight:600">
                            {{ $log->user?->name ?? '<i class="text-muted">System</i>' }}
                        </div>
                        <small class="text-muted">{{ $log->ip_address }}</small>
                    </td>
                    <td>{!! $log->action_badge !!}</td>
                    <td>
                        <span class="badge badge-light border">
                            {{ $log->model_label }}
                        </span>
                    </td>
                    <td style="font-size:12px">
                        {{ $log->description }}
                        @if($log->old_values || $log->new_values)
                        <a href="{{ route('activity-log.show', $log) }}"
                           class="text-muted ml-1" style="font-size:10px">
                            <i class="fas fa-eye"></i> detail
                        </a>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('activity-log.destroy', $log) }}"
                              method="POST" class="d-inline"
                              data-confirm="Hapus log ini?">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-outline-danger">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="fas fa-history fa-2x d-block mb-2"></i>
                        Belum ada log aktivitas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer d-flex justify-content-between align-items-center">
        {{ $logs->links() }}
        <small class="text-muted">
            Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }}
            dari {{ $logs->total() }} log
        </small>
    </div>
</div>

@endsection
