@extends('layouts.app')

@section('title', 'Detail Peminjaman')
@section('page-title', 'Detail Peminjaman')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('peminjaman.index') }}">Peminjaman</a></li>
    <li class="breadcrumb-item active">{{ $peminjaman->no_pinjam }}</li>
@endsection

@section('content')
<div class="row">

    {{-- Info Header --}}
    <div class="col-md-5">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clipboard-list mr-2"></i>{{ $peminjaman->no_pinjam }}
                </h3>
                <div class="card-tools">{!! $peminjaman->status_badge !!}</div>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted" width="45%">Peminjam</td>
                        <td class="font-weight-bold">{{ $peminjaman->peminjam->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Departemen</td>
                        <td>{{ $peminjaman->peminjam->departemen ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jumlah Alat</td>
                        <td>
                            <span class="badge badge-info">
                                {{ $peminjaman->jumlahAlat() }} alat
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tgl Pinjam</td>
                        <td>{{ $peminjaman->tgl_pinjam->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Rencana Kembali</td>
                        <td class="{{ $peminjaman->isTerlambat() ? 'text-danger font-weight-bold' : '' }}">
                            {{ $peminjaman->tgl_kembali_rencana->format('d F Y') }}
                            @if($peminjaman->isTerlambat())
                                <span class="badge badge-danger ml-1">
                                    {{ $peminjaman->tgl_kembali_rencana->diffInDays(now()) }} hari terlambat
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Keperluan</td>
                        <td>{{ $peminjaman->keperluan ?? '—' }}</td>
                    </tr>
                    @if($peminjaman->catatan_petugas)
                    <tr>
                        <td class="text-muted">Catatan Petugas</td>
                        <td>{{ $peminjaman->catatan_petugas }}</td>
                    </tr>
                    @endif
                    @if($peminjaman->disetujuiOleh)
                    <tr>
                        <td class="text-muted">Diproses Oleh</td>
                        <td>{{ $peminjaman->disetujuiOleh->name }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        {{-- Panel Approve / Reject --}}
        @if($peminjaman->status === 'menunggu' && Auth::user()->canManage())
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-check-circle mr-2"></i>Setujui</h3>
            </div>
            <div class="card-body">
                <p class="text-muted text-sm mb-0">
                    Klik untuk menyetujui peminjaman semua alat di bawah.
                </p>
            </div>
            <div class="card-footer">
                <form action="{{ route('peminjaman.approve', $peminjaman) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fas fa-check mr-1"></i> Setujui Peminjaman
                    </button>
                </form>
            </div>
        </div>

        <div class="card card-danger card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-times-circle mr-2"></i>Tolak</h3>
            </div>
            <form action="{{ route('peminjaman.reject', $peminjaman) }}" method="POST">
                @csrf @method('PATCH')
                <div class="card-body">
                    <div class="form-group mb-0">
                        <textarea name="catatan_petugas" class="form-control" rows="2"
                                  placeholder="Alasan penolakan (opsional)..."></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-danger btn-block">
                        <i class="fas fa-times mr-1"></i> Tolak Peminjaman
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>

    {{-- Daftar Alat --}}
    <div class="col-md-7">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tools mr-2"></i>
                    Daftar Alat yang Dipinjam
                    <span class="badge badge-secondary ml-1">{{ $peminjaman->jumlahAlat() }}</span>
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Alat</th>
                            <th>Kondisi Kembali</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($peminjaman->detail as $i => $d)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td>
                                <div class="font-weight-bold">{{ $d->alat->nama }}</div>
                                <small class="text-muted">{{ $d->alat->kode }}</small>
                            </td>
                            <td>
                                <small>{{ $d->kondisi_kembali ?? '—' }}</small>
                            </td>
                            <td>
                                <small>{{ $d->tgl_kembali_aktual?->format('d/m/Y') ?? '—' }}</small>
                            </td>
                            <td>
                                @if($d->sudahDikembalikan())
                                    <span class="badge badge-success">Kembali</span>
                                @else
                                    <span class="badge badge-warning">Dipinjam</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Form Pengembalian --}}
        @if($peminjaman->status === 'dipinjam' && Auth::user()->canManage())
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-undo mr-2"></i>Proses Pengembalian
                </h3>
            </div>
            <form action="{{ route('peminjaman.kembali', $peminjaman) }}" method="POST">
                @csrf @method('PATCH')
                <div class="card-body p-0">
                    @foreach($peminjaman->detail->where('tgl_kembali_aktual', null) as $i => $d)
                    <div class="p-3 border-bottom">
                        <input type="hidden" name="detail[{{ $i }}][alat_id]" value="{{ $d->alat_id }}">
                        <div class="font-weight-bold mb-2">
                            <i class="fas fa-wrench mr-1 text-muted"></i>
                            {{ $d->alat->nama }}
                            <code class="ml-1">{{ $d->alat->kode }}</code>
                        </div>
                        <div class="row">
                            <div class="col-md-7">
                                <div class="form-group mb-0">
                                    <label class="text-sm">Kondisi Alat <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="detail[{{ $i }}][kondisi]"
                                           class="form-control form-control-sm"
                                           placeholder="Contoh: Baik, tidak ada kerusakan"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group mb-0">
                                    <label class="text-sm">Status Setelah Kembali <span class="text-danger">*</span></label>
                                    <select name="detail[{{ $i }}][status_alat]"
                                            class="form-control form-control-sm" required>
                                        <option value="tersedia">✅ Tersedia</option>
                                        <option value="rusak">❌ Rusak</option>
                                        <option value="servis">🔧 Perlu Servis</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning btn-block">
                        <i class="fas fa-save mr-1"></i>
                        Catat Pengembalian
                        ({{ $peminjaman->detail->where('tgl_kembali_aktual', null)->count() }} Alat)
                    </button>
                </div>
            </form>
        </div>
        @endif

    </div>
</div>

<a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
</a>
@endsection