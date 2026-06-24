{{-- resources/views/alat/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Alat')
@section('page-title', 'Edit Alat Kerja')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('alat.index') }}">Alat Kerja</a></li>
    <li class="breadcrumb-item active">Edit — {{ $alat->nama }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-md-9">

<div class="card card-outline card-warning">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-edit mr-2"></i>Edit: <strong>{{ $alat->nama }}</strong>
            <code class="ml-2">{{ $alat->kode }}</code>
        </h3>
    </div>

    <form action="{{ route('alat.update', $alat) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="card-body">

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Nama Alat <span class="text-danger">*</span></label>
                        <input type="text" name="nama"
                               class="form-control @error('nama') is-invalid @enderror"
                               value="{{ old('nama', $alat->nama) }}" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Kategori <span class="text-danger">*</span></label>
                        <select name="kategori_id"
                                class="form-control select2 @error('kategori_id') is-invalid @enderror"
                                required>
                            <option value="">— Pilih —</option>
                            @foreach($kategori as $k)
                            <option value="{{ $k->id }}"
                                {{ old('kategori_id', $alat->kategori_id) == $k->id ? 'selected':'' }}>
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
                        <input type="text" name="merk" class="form-control"
                               value="{{ old('merk', $alat->merk) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>No. Seri</label>
                        <input type="text" name="no_seri" class="form-control"
                               value="{{ old('no_seri', $alat->no_seri) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            @foreach(['tersedia','dipinjam','rusak','servis'] as $s)
                            <option value="{{ $s }}"
                                {{ old('status', $alat->status) == $s ? 'selected':'' }}>
                                {{ ucfirst($s) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Lokasi / Gudang</label>
                        <input type="text" name="lokasi" class="form-control"
                               value="{{ old('lokasi', $alat->lokasi) }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Tanggal Beli</label>
                        <input type="date" name="tgl_beli" class="form-control"
                               value="{{ old('tgl_beli', $alat->tgl_beli?->format('Y-m-d')) }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Harga Beli (Rp)</label>
                        <input type="number" name="harga_beli" class="form-control" min="0"
                               value="{{ old('harga_beli', $alat->harga_beli) }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $alat->keterangan) }}</textarea>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ $alat->foto ? 'Ganti Foto' : 'Foto Alat' }}</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="foto"
                                   id="foto" accept="image/*">
                            <label class="custom-file-label" for="foto">Pilih foto...</label>
                        </div>
                        @if($alat->foto)
                        <div class="mt-2">
                            <img src="{{ Storage::url($alat->foto) }}"
                                 alt="Foto alat" class="img-thumbnail" style="max-height:80px">
                        </div>
                        @endif
                        <small class="text-muted">Kosongkan jika tidak ingin mengganti.</small>
                    </div>
                </div>
            </div>

        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save mr-1"></i> Simpan Perubahan
                </button>
                <a href="{{ route('alat.show', $alat) }}" class="btn btn-secondary ml-2">
                    <i class="fas fa-times mr-1"></i> Batal
                </a>
            </div>
            @if(Auth::user()->isAdmin())
            <button type="button"
                    class="btn btn-outline-danger btn-sm"
                    onclick="confirmHapus()">
                <i class="fas fa-trash mr-1"></i> Hapus
            </button>
            @endif
        </div>

    </form>
    		@if(Auth::user()->isAdmin())
			<form action="{{ route('alat.destroy', $alat) }}"
      		method="POST"
      		id="formHapusAlat">
    			@csrf @method('DELETE')            
    			</form>
           @endif
</div>

</div>
</div>
@endsection

@push('scripts')
<script>
function confirmHapus() {
    Swal.fire({
        title: 'Hapus Alat?',
        text: 'Alat "{{ $alat->nama }}" akan dihapus permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formHapusAlat').submit();
        }
    });
}

// Custom file label
$('#foto').on('change', function () {
    $(this).next('.custom-file-label')
           .text($(this).val().split('\\').pop() || 'Pilih foto...');
});
</script>
@endpush