{{-- resources/views/users/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Manajemen User</a></li>
    <li class="breadcrumb-item active">Edit — {{ $user->name }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-md-8">

<div class="card card-outline card-warning">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-user-edit mr-2"></i>Edit User:
            <strong>{{ $user->name }}</strong>
            {!! $user->role_badge !!}
        </h3>
    </div>

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">

            @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Terdapat kesalahan:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Informasi Akun --}}
            <h6 class="text-muted font-weight-bold text-uppercase mb-3" style="font-size:11px;letter-spacing:.08em">
                <i class="fas fa-key mr-1"></i> Informasi Akun
            </h6>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-control @error('role') is-invalid @enderror"
                                {{ $user->id === Auth::id() ? 'disabled' : '' }} required>
                            <option value="admin"    {{ old('role',$user->role)=='admin'    ? 'selected':'' }}>Admin</option>
                            <option value="petugas"  {{ old('role',$user->role)=='petugas'  ? 'selected':'' }}>Petugas</option>
                            <option value="peminjam" {{ old('role',$user->role)=='peminjam' ? 'selected':'' }}>Peminjam</option>
                        </select>
                        @if($user->id === Auth::id())
                            {{-- Kirim value hidden jika field di-disabled --}}
                            <input type="hidden" name="role" value="{{ $user->role }}">
                            <small class="text-muted"><i class="fas fa-lock mr-1"></i>Tidak dapat mengubah role sendiri.</small>
                        @endif
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Password Baru <small class="text-muted">(kosongkan jika tidak diubah)</small></label>
                        <div class="input-group">
                            <input type="password" name="password" id="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Minimal 8 karakter">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>
                        @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <hr>

            {{-- Informasi Profil --}}
            <h6 class="text-muted font-weight-bold text-uppercase mb-3" style="font-size:11px;letter-spacing:.08em">
                <i class="fas fa-id-card mr-1"></i> Informasi Profil
            </h6>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>No. Induk / NIP / NIK</label>
                        <input type="text" name="no_induk"
                               class="form-control @error('no_induk') is-invalid @enderror"
                               value="{{ old('no_induk', $user->no_induk) }}">
                        @error('no_induk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Departemen / Divisi</label>
                        <input type="text" name="departemen"
                               class="form-control @error('departemen') is-invalid @enderror"
                               value="{{ old('departemen', $user->departemen) }}">
                        @error('departemen')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" name="telepon"
                               class="form-control @error('telepon') is-invalid @enderror"
                               value="{{ old('telepon', $user->telepon) }}">
                        @error('telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <hr>

            {{-- Status Akun --}}
            <h6 class="text-muted font-weight-bold text-uppercase mb-3" style="font-size:11px;letter-spacing:.08em">
                <i class="fas fa-toggle-on mr-1"></i> Status Akun
            </h6>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="is_active"
                           name="is_active" value="1"
                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                           {{ $user->id === Auth::id() ? 'disabled' : '' }}>
                    <label class="custom-control-label" for="is_active">
                        Akun Aktif
                        <small class="text-muted ml-1">(nonaktifkan untuk memblokir akses login)</small>
                    </label>
                </div>
                @if($user->id === Auth::id())
                    <input type="hidden" name="is_active" value="1">
                    <small class="text-muted d-block mt-1"><i class="fas fa-lock mr-1"></i>Tidak dapat menonaktifkan akun sendiri.</small>
                @endif
            </div>

            {{-- Info terakhir login --}}
            <div class="callout callout-info mt-2 mb-0" style="font-size:12px">
                <i class="fas fa-info-circle mr-1"></i>
                User ini terdaftar sejak <strong>{{ $user->created_at->format('d F Y') }}</strong>.
                @if($user->peminjaman()->count() > 0)
                    Total peminjaman: <strong>{{ $user->peminjaman()->count() }}</strong> transaksi.
                @endif
            </div>

        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-save mr-1"></i> Simpan Perubahan
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary ml-2">
                <i class="fas fa-times mr-1"></i> Batal
            </a>
        </div>
    </form>
</div>

</div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
@endpush
