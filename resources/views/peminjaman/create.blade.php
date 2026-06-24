{{-- resources/views/peminjaman/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Ajukan Peminjaman')
@section('page-title', 'Ajukan Peminjaman Alat')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('peminjaman.index') }}">Peminjaman</a></li>
    <li class="breadcrumb-item active">Ajukan</li>
@endsection

@section('content')
<div class="row">

    {{-- ── Form Kiri ────────────────────────────────── --}}
    <div class="col-md-8">
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-plus-circle mr-2"></i>Form Peminjaman Alat
            </h3>
        </div>
        <form action="{{ route('peminjaman.store') }}" method="POST" id="formPeminjaman">
            @csrf
            <div class="card-body">

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                @endif

                {{-- Pilih Alat --}}
                <div class="form-group">
                    <label class="font-weight-bold">
                        Pilih Alat yang Dipinjam
                        <span class="text-danger">*</span>
                        <small class="text-muted font-weight-normal ml-1">(Maks. 10 alat)</small>
                    </label>

                    {{-- Search alat --}}
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" id="searchAlat" class="form-control"
                               placeholder="Cari nama / kode alat...">
                    </div>

                    {{-- Daftar alat tersedia --}}
                    <div class="border rounded" style="max-height:320px;overflow-y:auto">
                       @forelse($alat as $a)
				<div class="alat-item d-flex align-items-center p-2 border-bottom"
     				style="cursor:pointer; transition:background .15s"
    	 			onmouseover="this.style.background='#f8f9fa'"
     				onmouseout="this.style.background=''">
 
    				<div class="custom-control custom-checkbox mr-3" style="flex-shrink:0">
        			<input type="checkbox"
               			class="custom-control-input alat-check"
               			id="alat_{{ $a->id }}"
               			name="alat_ids[]"
               			value="{{ $a->id }}">
        			<label class="custom-control-label" for="alat_{{ $a->id }}"></label>
    			</div>
 
    			<div style="flex:1; min-width:0">
        			{{-- Teks dengan class khusus agar JavaScript bisa search --}}
        			<div class="font-weight-bold alat-nama" style="font-size:13px">
            			{{ $a->nama }}
        			</div>
        			<small class="text-muted">
            			<code class="alat-kode">{{ $a->kode }}</code>
            			&bull;
            			<span class="alat-kategori">{{ $a->kategori->nama }}</span>
            			@if($a->merk)
                				&bull; <span class="alat-merk">{{ $a->merk }}</span>
            			@else
                				<span class="alat-merk d-none"></span>
            			@endif
            			@if($a->lokasi)
               				&bull; <i class="fas fa-map-marker-alt"></i>
                				<span class="alat-lokasi">{{ $a->lokasi }}</span>
            			@else
                				<span class="alat-lokasi d-none"></span>
            			@endif
        			</small>
    			</div>
 
    			<span class="badge badge-success ml-2" style="flex-shrink:0">Tersedia</span>
		</div>
		@empty
		<div class="text-center text-muted py-4">
    			<i class="fas fa-tools fa-2x d-block mb-2"></i>
    			Tidak ada alat tersedia saat ini.
		</div>
		@endforelse
 
		{{-- Pesan jika pencarian tidak ada hasil --}}
		<div id="noResult" class="text-center text-muted py-3 d-none">
    			<i class="fas fa-search fa-lg d-block mb-2"></i>
    			<small>Tidak ada alat yang cocok dengan "<span id="keywordLabel"></span>"</small>
		</div>

                    {{-- Counter --}}
                    <div class="mt-2 d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <span id="counterDipilih">0</span> alat dipilih
                        </small>
                        <small id="warningMax" class="text-danger d-none">
                            <i class="fas fa-exclamation-triangle"></i> Maksimal 10 alat!
                        </small>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Pinjam <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_pinjam"
                                   class="form-control @error('tgl_pinjam') is-invalid @enderror"
                                   value="{{ old('tgl_pinjam', now()->toDateString()) }}"
                                   min="{{ now()->toDateString() }}" required>
                            @error('tgl_pinjam')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Rencana Kembali <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_kembali_rencana"
                                   class="form-control @error('tgl_kembali_rencana') is-invalid @enderror"
                                   value="{{ old('tgl_kembali_rencana', now()->addDays(7)->toDateString()) }}"
                                   min="{{ now()->addDay()->toDateString() }}" required>
                            @error('tgl_kembali_rencana')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Keperluan</label>
                    <textarea name="keperluan" class="form-control" rows="3"
                              placeholder="Jelaskan keperluan peminjaman alat ini...">{{ old('keperluan') }}</textarea>
                </div>

                @if(Auth::user()->canManage())
                <div class="callout callout-info">
                    <i class="fas fa-info-circle mr-1"></i>
                    Sebagai petugas/admin, peminjaman akan langsung disetujui.
                </div>
                @else
                <div class="callout callout-warning">
                    <i class="fas fa-hourglass-half mr-1"></i>
                    Permohonan akan menunggu persetujuan petugas.
                </div>
                @endif

            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <div>
                    <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>
                        <i class="fas fa-paper-plane mr-1"></i>
                        Ajukan Peminjaman (<span id="btnCounter">0</span> Alat)
                    </button>
                    <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-times mr-1"></i> Batal
                    </a>
                </div>
                <small class="text-muted"><span class="text-danger">*</span> Wajib diisi</small>
            </div>
        </form>
    </div>
    </div>

    {{-- ── Panel Alat Dipilih ──────────────────────── --}}
    <div class="col-md-4">
        <div class="card card-outline card-success sticky-top" style="top:70px">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clipboard-check mr-2"></i>
                    Alat Dipilih
                    <span class="badge badge-success ml-1" id="badgeCounter">0</span>
                </h3>
            </div>
            <div class="card-body p-0">
                <div id="selectedList" style="min-height:60px">
                    <div id="emptyMsg" class="text-center text-muted py-4">
                        <i class="fas fa-clipboard fa-2x d-block mb-2"></i>
                        <small>Belum ada alat dipilih.<br>Centang alat di sebelah kiri.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Langsung jalankan tanpa wrapper DOMContentLoaded atau $(function)
(function () {
    const MAX = 10;
    const searchInput = document.getElementById('searchAlat');
    const noResult = document.getElementById('noResult');

   const checkboxes = document.querySelectorAll('.alat-check');
    const btn = document.getElementById('btnSubmit');
    const counter = document.getElementById('btnCounter');
    const keperluan = document.querySelector('textarea[name="keperluan"]');

    // ✅ UPDATE BUTTON (CORE LOGIC)
    function updateButton() {
        const checked = document.querySelectorAll('.alat-check:checked').length;
        const isiKeperluan = keperluan.value.trim();

        counter.textContent = checked;

        if (checked > 0 && isiKeperluan !== '') {
            btn.disabled = false;
        } else {
            btn.disabled = true;
        }

        // warning max
        if (checked >= MAX) {
            document.getElementById('warningMax')?.classList.remove('d-none');
        } else {
            document.getElementById('warningMax')?.classList.add('d-none');
        }
    }

    // ✅ CHECKBOX EVENT
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function () {

            const checked = document.querySelectorAll('.alat-check:checked').length;

            // batasi max 10
            if (checked > MAX) {
                this.checked = false;
                return;
            }

            updateButton();
        });
    });

    // ✅ KEPERLUAN EVENT
    keperluan.addEventListener('input', updateButton);

    keperluan.addEventListener('blur', function () {
        if (this.value.trim() === '') {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
    });

    if (!searchInput) { console.error('searchAlat not found!'); return; }

    searchInput.addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        const items = document.querySelectorAll('.alat-item');
        let visible = 0;

        items.forEach(function (item) {
            const nama   = (item.querySelector('.alat-nama')   || {}).textContent || '';
            const kode   = (item.querySelector('.alat-kode')   || {}).textContent || '';
            const merk   = (item.querySelector('.alat-merk')   || {}).textContent || '';
            const lokasi = (item.querySelector('.alat-lokasi') || {}).textContent || '';

            const match = q === ''
                || nama.toLowerCase().includes(q)
                || kode.toLowerCase().includes(q)
                || merk.toLowerCase().includes(q)
                || lokasi.toLowerCase().includes(q);

               if (match) {
            item.style.cssText = 'cursor:pointer; transition:background .15s';
        } else {
            item.style.cssText = 'display:none !important';
        }
        if (match) visible++;
    });
        noResult.classList.toggle('d-none', !(visible === 0 && q !== ''));
    });
})();
</script>
@endpush
