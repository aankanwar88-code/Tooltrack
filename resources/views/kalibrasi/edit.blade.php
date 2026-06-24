{{-- resources/views/kalibrasi/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Kalibrasi')
@section('page-title', 'Edit Data Kalibrasi')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('kalibrasi.index') }}">Kalibrasi</a></li>
    <li class="breadcrumb-item"><a href="{{ route('kalibrasi.show', $kalibrasi) }}">{{ $kalibrasi->no_kalibrasi }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-md-9">

<div class="card card-outline card-warning">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-edit mr-2"></i>Edit Kalibrasi:
            <strong>{{ $kalibrasi->no_kalibrasi }}</strong>
        </h3>
        <div class="card-tools">
            {!! $kalibrasi->hasil_badge !!}
        </div>
    </div>

    <form action="{{ route('kalibrasi.update', $kalibrasi) }}" method="POST"
          enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="card-body">

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Terdapat kesalahan:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            {{-- ── Informasi Alat ───────────────────────── --}}
            <h6 class="text-muted font-weight-bold text-uppercase mb-3"
                style="font-size:11px;letter-spacing:.08em">
                <i class="fas fa-tools mr-1"></i> Informasi Alat
            </h6>

            <div class="form-group">
                <label>Pilih Alat <span class="text-danger">*</span></label>
                <select name="alat_id"
                        class="form-control select2 @error('alat_id') is-invalid @enderror"
                        id="alatSelect" required>
                    <option value="">— Pilih Alat —</option>
                    @foreach($alat as $a)
                    <option value="{{ $a->id }}"
                            data-kode="{{ $a->kode }}"
                            data-kategori="{{ $a->kategori->nama }}"
                            data-status="{{ $a->status }}"
                            {{ old('alat_id', $kalibrasi->alat_id) == $a->id ? 'selected' : '' }}>
                        [{{ $a->kode }}] {{ $a->nama }}
                        @if($a->merk) — {{ $a->merk }} @endif
                    </option>
                    @endforeach
                </select>
                @error('alat_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Info alat terpilih --}}
            <div id="alatInfo" class="callout callout-info mb-3" style="font-size:12px">
                <i class="fas fa-info-circle mr-1"></i>
                <strong>Kode:</strong> <span id="infoKode">{{ $kalibrasi->alat->kode }}</span>
                &nbsp;|&nbsp;
                <strong>Kategori:</strong> <span id="infoKategori">{{ $kalibrasi->alat->kategori->nama }}</span>
                &nbsp;|&nbsp;
                <strong>Status:</strong> <span id="infoStatus">{{ $kalibrasi->alat->status }}</span>
            </div>

            <hr>

            {{-- ── Jadwal Kalibrasi ─────────────────────── --}}
            <h6 class="text-muted font-weight-bold text-uppercase mb-3"
                style="font-size:11px;letter-spacing:.08em">
                <i class="fas fa-calendar-alt mr-1"></i> Jadwal Kalibrasi
            </h6>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Kalibrasi <span class="text-danger">*</span></label>
                        <input type="date" name="tgl_kalibrasi"
                               id="tglKalibrasi"
                               class="form-control @error('tgl_kalibrasi') is-invalid @enderror"
                               value="{{ old('tgl_kalibrasi', $kalibrasi->tgl_kalibrasi->format('Y-m-d')) }}"
                               required>
                        @error('tgl_kalibrasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Kalibrasi Berikutnya <span class="text-danger">*</span></label>
                        <input type="date" name="tgl_kalibrasi_berikutnya"
                               id="tglBerikutnya"
                               class="form-control @error('tgl_kalibrasi_berikutnya') is-invalid @enderror"
                               value="{{ old('tgl_kalibrasi_berikutnya', $kalibrasi->tgl_kalibrasi_berikutnya->format('Y-m-d')) }}"
                               required>
                        @error('tgl_kalibrasi_berikutnya')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            <i class="fas fa-lightbulb"></i>
                            Interval:
                            <span id="intervalInfo" class="font-weight-bold text-info">—</span>
                        </small>
                    </div>
                </div>
            </div>

            <hr>

            {{-- ── Hasil & Lembaga ─────────────────────── --}}
            <h6 class="text-muted font-weight-bold text-uppercase mb-3"
                style="font-size:11px;letter-spacing:.08em">
                <i class="fas fa-clipboard-check mr-1"></i> Hasil & Lembaga Kalibrasi
            </h6>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Hasil Kalibrasi <span class="text-danger">*</span></label>
                        <select name="hasil"
                                class="form-control @error('hasil') is-invalid @enderror"
                                id="hasilSelect" required>
                            <option value="">— Pilih Hasil —</option>
                            <option value="lulus"
                                    {{ old('hasil', $kalibrasi->hasil) == 'lulus' ? 'selected' : '' }}>
                                ✅ Lulus
                            </option>
                            <option value="tidak_lulus"
                                    {{ old('hasil', $kalibrasi->hasil) == 'tidak_lulus' ? 'selected' : '' }}>
                                ❌ Tidak Lulus
                            </option>
                            <option value="perlu_perbaikan"
                                    {{ old('hasil', $kalibrasi->hasil) == 'perlu_perbaikan' ? 'selected' : '' }}>
                                ⚠️ Perlu Perbaikan
                            </option>
                        </select>
                        @error('hasil')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Lembaga Kalibrasi</label>
                        <input type="text" name="lembaga_kalibrasi"
                               class="form-control @error('lembaga_kalibrasi') is-invalid @enderror"
                               value="{{ old('lembaga_kalibrasi', $kalibrasi->lembaga_kalibrasi) }}"
                               placeholder="Nama lembaga / lab kalibrasi">
                        @error('lembaga_kalibrasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>No. Sertifikat</label>
                        <input type="text" name="no_sertifikat"
                               class="form-control @error('no_sertifikat') is-invalid @enderror"
                               value="{{ old('no_sertifikat', $kalibrasi->no_sertifikat) }}"
                               placeholder="Nomor sertifikat kalibrasi">
                        @error('no_sertifikat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Biaya Kalibrasi (Rp)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="number" name="biaya"
                                   class="form-control @error('biaya') is-invalid @enderror"
                                   value="{{ old('biaya', $kalibrasi->biaya) }}"
                                   placeholder="0" min="0">
                        </div>
                        @error('biaya')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Keterangan</label>
                        <input type="text" name="keterangan"
                               class="form-control @error('keterangan') is-invalid @enderror"
                               value="{{ old('keterangan', $kalibrasi->keterangan) }}"
                               placeholder="Catatan hasil kalibrasi, kondisi alat, rekomendasi, dll.">
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <hr>

            {{-- ── Upload Dokumen ──────────────────────── --}}
            <h6 class="text-muted font-weight-bold text-uppercase mb-3"
                style="font-size:11px;letter-spacing:.08em">
                <i class="fas fa-paperclip mr-1"></i> Dokumen Sertifikat
            </h6>

            <div class="row align-items-start">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>
                            {{ $kalibrasi->dokumen ? 'Ganti Dokumen' : 'Upload Dokumen' }}
                        </label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input"
                                   name="dokumen" id="dokumen"
                                   accept=".pdf,.jpg,.jpeg,.png">
                            <label class="custom-file-label" for="dokumen">
                                Pilih file baru...
                            </label>
                        </div>
                        <small class="text-muted">
                            Format: PDF, JPG, PNG — Maks. 5 MB
                            @if($kalibrasi->dokumen)
                                <br>Kosongkan jika tidak ingin mengganti dokumen.
                            @endif
                        </small>
                        @error('dokumen')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Dokumen saat ini --}}
                @if($kalibrasi->dokumen)
                <div class="col-md-6">
                    <label>Dokumen Saat Ini</label>
                    <div class="d-flex align-items-center p-2 bg-light rounded border"
                         style="gap:10px">
                        @php
                            $ext = pathinfo($kalibrasi->dokumen, PATHINFO_EXTENSION);
                        @endphp
                        @if(in_array(strtolower($ext), ['jpg','jpeg','png']))
                            <i class="fas fa-file-image fa-2x text-info"></i>
                        @else
                            <i class="fas fa-file-pdf fa-2x text-danger"></i>
                        @endif
                        <div>
                            <div class="font-weight-bold text-sm">
                                {{ basename($kalibrasi->dokumen) }}
                            </div>
                            <a href="{{ Storage::url($kalibrasi->dokumen) }}"
                               target="_blank"
                               class="btn btn-xs btn-outline-primary mt-1">
                                <i class="fas fa-eye mr-1"></i>Lihat Dokumen
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Alert hasil tidak lulus --}}
            <div id="alertTidakLulus"
                 class="callout callout-danger {{ in_array($kalibrasi->hasil, ['tidak_lulus','perlu_perbaikan']) ? '' : 'd-none' }}">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Perhatian!</strong> Jika hasil kalibrasi <strong>Tidak Lulus</strong>
                atau <strong>Perlu Perbaikan</strong>, status alat akan otomatis diubah
                menjadi <strong>Servis</strong> setelah data disimpan.
            </div>

            {{-- Info record --}}
            <div class="callout callout-secondary mt-3 mb-0" style="font-size:12px">
                <i class="fas fa-history mr-1"></i>
                Data kalibrasi ini dibuat pada
                <strong>{{ $kalibrasi->created_at->format('d F Y, H:i') }}</strong>
                oleh <strong>{{ $kalibrasi->dilakukanOleh->name }}</strong>.
                @if($kalibrasi->created_at != $kalibrasi->updated_at)
                    Terakhir diperbarui: <strong>{{ $kalibrasi->updated_at->format('d F Y, H:i') }}</strong>.
                @endif
            </div>

        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <div>
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save mr-1"></i> Simpan Perubahan
                </button>
                <a href="{{ route('kalibrasi.show', $kalibrasi) }}"
                   class="btn btn-secondary ml-2">
                    <i class="fas fa-times mr-1"></i> Batal
                </a>
            </div>
            @if(Auth::user()->isAdmin())
            <form action="{{ route('kalibrasi.destroy', $kalibrasi) }}"
                  method="POST"
                  data-confirm="Hapus data kalibrasi {{ $kalibrasi->no_kalibrasi }}? Tindakan ini tidak dapat dibatalkan.">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-trash mr-1"></i> Hapus Data
                </button>
            </form>
            @endif
        </div>
    </form>
</div>

</div>
</div>
@endsection

@push('scripts')
<script>
$(function () {

    // ── Info alat terpilih ─────────────────────────
    $('#alatSelect').on('change', function () {
        const opt = $(this).find(':selected');
        if (opt.val()) {
            $('#infoKode').text(opt.data('kode') || '—');
            $('#infoKategori').text(opt.data('kategori') || '—');
            $('#infoStatus').text(opt.data('status') || '—');
            $('#alatInfo').removeClass('d-none');
        } else {
            $('#alatInfo').addClass('d-none');
        }
    });

    // ── Hitung interval kalibrasi ──────────────────
    function hitungInterval() {
        const tgl1 = $('#tglKalibrasi').val();
        const tgl2 = $('#tglBerikutnya').val();
        if (tgl1 && tgl2) {
            const d1    = new Date(tgl1);
            const d2    = new Date(tgl2);
            const diff  = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));
            const bulan = Math.round(diff / 30);
            if (diff > 0) {
                $('#intervalInfo').text(
                    diff < 30
                        ? diff + ' hari'
                        : bulan + ' bulan (' + diff + ' hari)'
                ).removeClass('text-danger').addClass('text-info');
            } else {
                $('#intervalInfo').text('Tanggal tidak valid!')
                    .removeClass('text-info').addClass('text-danger');
            }
        }
    }

    $('#tglKalibrasi, #tglBerikutnya').on('change', hitungInterval);
    hitungInterval();

    // ── Alert tidak lulus ──────────────────────────
    $('#hasilSelect').on('change', function () {
        const val = $(this).val();
        if (val === 'tidak_lulus' || val === 'perlu_perbaikan') {
            $('#alertTidakLulus').removeClass('d-none');
        } else {
            $('#alertTidakLulus').addClass('d-none');
        }
    });

    // ── Custom file label ──────────────────────────
    $('#dokumen').on('change', function () {
        const name = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').text(name || 'Pilih file...');
    });

});
</script>
@endpush
