{{-- resources/views/alat/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Alat')
@section('page-title', 'Tambah Alat Kerja')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('alat.index') }}">Alat Kerja</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-md-9">

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-plus-circle mr-2"></i>Form Tambah Alat</h3>
    </div>
    <form action="{{ route('alat.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Nama Alat <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                               value="{{ old('nama') }}" placeholder="Nama lengkap alat kerja" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Kategori <span class="text-danger">*</span></label>
                        <select name="kategori_id" class="form-control select2 @error('kategori_id') is-invalid @enderror" required>
                            <option value="">— Pilih Kategori —</option>
                            @foreach($kategori as $k)
                                <option value="{{ $k->id }}" {{ old('kategori_id')==$k->id ? 'selected':'' }}>
                                    {{ $k->nama }} ({{ $k->kode }})
                                </option>
                            @endforeach
                        </select>
                        @error('kategori_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Merk / Produsen</label>
                        <input type="text" name="merk" class="form-control" value="{{ old('merk') }}" placeholder="Contoh: Bosch, Makita">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>No. Seri</label>
                        <input type="text" name="no_seri" class="form-control" value="{{ old('no_seri') }}" placeholder="Serial number">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="tersedia" {{ old('status','tersedia')=='tersedia' ? 'selected':'' }}>Tersedia</option>
                            <option value="rusak"    {{ old('status')=='rusak'   ? 'selected':'' }}>Rusak</option>
                            <option value="servis"   {{ old('status')=='servis'  ? 'selected':'' }}>Servis</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Lokasi / Gudang</label>
                        <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi') }}" placeholder="Contoh: Gudang A, Rak 2">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Tanggal Beli</label>
                        <input type="date" name="tgl_beli" class="form-control" value="{{ old('tgl_beli') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Harga Beli (Rp)</label>
                        <input type="number" name="harga_beli" class="form-control" value="{{ old('harga_beli') }}" min="0" placeholder="0">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Catatan tambahan...">{{ old('keterangan') }}</textarea>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Foto Alat</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="foto" id="foto" accept="image/*">
                            <label class="custom-file-label" for="foto">Pilih foto...</label>
                        </div>
                        <small class="text-muted">Maks. 2 MB (jpg, png)</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Simpan Alat
            </button>
            <a href="{{ route('alat.index') }}" class="btn btn-secondary ml-2">
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
$('#foto').on('change', function () {
    const name = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').text(name || 'Pilih foto...');
});
</script>
@endpush
