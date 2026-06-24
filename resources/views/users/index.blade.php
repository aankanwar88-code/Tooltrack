{{-- resources/views/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')
@section('breadcrumb')
    <li class="breadcrumb-item active">Manajemen User</li>
@endsection

@section('content')

{{-- Filter --}}
<div class="card card-outline card-secondary">
    <div class="card-body">
        <form method="GET" action="{{ route('users.index') }}" class="form-inline flex-wrap" style="gap:.5rem">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Cari nama / email / no. induk..." value="{{ request('search') }}" style="min-width:260px">
            <select name="role" class="form-control form-control-sm">
                <option value="">Semua Role</option>
                <option value="admin"    {{ request('role')=='admin'    ? 'selected':'' }}>Admin</option>
                <option value="petugas"  {{ request('role')=='petugas'  ? 'selected':'' }}>Petugas</option>
                <option value="peminjam" {{ request('role')=='peminjam' ? 'selected':'' }}>Peminjam</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Filter</button>
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-redo"></i> Reset</a>
            <a href="{{ route('users.create') }}" class="btn btn-sm btn-success ml-auto">
                <i class="fas fa-user-plus"></i> Tambah User
            </a>
        </form>
    </div>
</div>

{{-- Statistik singkat --}}
<div class="row mb-3">
    <div class="col-sm-4">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-danger"><i class="fas fa-user-shield"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Admin</span>
                <span class="info-box-number">{{ $users->where('role','admin')->count() }}</span>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-primary"><i class="fas fa-user-tie"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Petugas</span>
                <span class="info-box-number">{{ $users->where('role','petugas')->count() }}</span>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-secondary"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Peminjam</span>
                <span class="info-box-number">{{ $users->where('role','peminjam')->count() }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-body p-0">
        <table class="table table-bordered table-hover table-sm mb-0">
            <thead class="thead-light">
                <tr>
                    <th width="40">#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No. Induk</th>
                    <th>Departemen</th>
                    <th>Telepon</th>
                    <th width="90">Role</th>
                    <th width="80">Status</th>
                    <th width="110">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $user)
                <tr>
                    <td class="text-center text-muted">{{ $users->firstItem() + $i }}</td>
                    <td>
                        <div class="d-flex align-items-center" style="gap:8px">
                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white"
                                 style="width:30px;height:30px;font-size:12px;font-weight:700;flex-shrink:0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-weight-bold" style="line-height:1.2">{{ $user->name }}</div>
                                @if($user->id === Auth::id())
                                    <small class="text-muted">(Anda)</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="font-size:12px">{{ $user->email }}</td>
                    <td><code>{{ $user->no_induk ?? '—' }}</code></td>
                    <td>{{ $user->departemen ?? '—' }}</td>
                    <td style="font-size:12px">{{ $user->telepon ?? '—' }}</td>
                    <td>{!! $user->role_badge !!}</td>
                    <td>
                        @if($user->is_active)
                            <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Aktif</span>
                        @else
                            <span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i>Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-xs btn-warning" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if($user->id !== Auth::id())
                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline"
                              data-confirm="Hapus user {{ $user->name }}? Tindakan ini tidak dapat dibatalkan.">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-danger" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        <i class="fas fa-users fa-2x d-block mb-2"></i>
                        Tidak ada user ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        {{ $users->links() }}
        <span class="text-muted small">Total: {{ $users->total() }} user</span>
    </div>
</div>

@endsection
