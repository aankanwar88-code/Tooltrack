{{-- resources/views/users/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah User')
@section('page-title', 'Tambah User Baru')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Manajemen User</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-md-8">

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-plus mr-2"></i>Form Tambah User</h3>
    </div>

    <form action="{{ route('users.store') }}" method="POST">
        @csrf
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
                               value="{{ old('name') }}" placeholder="Nama lengkap user" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                            <option value="">— Pilih Role —</option>
                            <option value="admin"    {{ old('role')=='admin'    ? 'selected':'' }}>
                                Admin — Akses penuh
                            </option>
                            <option value="petugas"  {{ old('role')=='petugas'  ? 'selected':'' }}>
                                Petugas — Kelola alat & peminjaman
                            </option>
                            <option value="peminjam" {{ old('role')=='peminjam' ? 'selected':'' }}>
                                Peminjam — Ajukan peminjaman
                            </option>
                        </select>
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
                               value="{{ old('email') }}" placeholder="email@contoh.com" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password" id="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Minimal 8 karakter" required>
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
                               value="{{ old('no_induk') }}" placeholder="Contoh: EMP-001">
                        @error('no_induk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Departemen / Divisi</label>
                        <input type="text" name="departemen"
                               class="form-control @error('departemen') is-invalid @enderror"
                               value="{{ old('departemen') }}" placeholder="Contoh: Teknik, Gudang">
                        @error('departemen')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" name="telepon"
                               class="form-control @error('telepon') is-invalid @enderror"
                               value="{{ old('telepon') }}" placeholder="Contoh: 08123456789">
                        @error('telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Info role --}}
            <div class="callout callout-info mt-2">
                <h6><i class="fas fa-info-circle mr-1"></i> Perbedaan Role</h6>
                <table class="table table-sm table-borderless mb-0" style="font-size:12px">
                    <tr>
                        <td width="80"><span class="badge badge-danger">Admin</span></td>
                        <td>Akses penuh: kelola user, alat, peminjaman, kalibrasi, laporan</td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-primary">Petugas</span></td>
                        <td>Kelola alat, setujui/tolak peminjaman, catat kalibrasi, export laporan</td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-secondary">Peminjam</span></td>
                        <td>Hanya dapat mengajukan dan melihat status peminjaman milik sendiri</td>
                    </tr>
                </table>
            </div>

        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Simpan User
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
