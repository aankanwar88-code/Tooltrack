{{-- resources/views/notifikasi/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page-title', 'Semua Notifikasi')
@section('breadcrumb')
    <li class="breadcrumb-item active">Notifikasi</li>
@endsection

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">
            <i class="far fa-bell mr-2"></i>Notifikasi
            @if($jumlahBelumDibaca > 0)
                <span class="badge badge-danger ml-1">{{ $jumlahBelumDibaca }} belum dibaca</span>
            @endif
        </h3>
        <div class="d-flex" style="gap:8px">
            @if($jumlahBelumDibaca > 0)
            <button class="btn btn-sm btn-outline-primary" onclick="bacaSemua()">
                <i class="fas fa-check-double mr-1"></i>Baca Semua
            </button>
            @endif
            @if($notifikasi->total() > 0)
            <form action="{{ route('notifikasi.hapus-semua') }}" method="POST"
                  data-confirm="Hapus semua notifikasi? Tindakan ini tidak dapat dibatalkan.">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-trash mr-1"></i>Hapus Semua
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="card-body p-0">
        @forelse($notifikasi as $n)
        <div class="d-flex align-items-start p-3 border-bottom
                    {{ $n->sudahDibaca() ? '' : 'bg-light' }}"
             style="gap:14px; transition: background .2s;">

            {{-- Icon --}}
            <div class="mt-1 text-center" style="width:32px;flex-shrink:0">
                <i class="{{ $n->icon }} fa-lg
                   {{ $n->tipe === 'danger'  ? 'text-danger'  :
                     ($n->tipe === 'warning' ? 'text-warning' :
                     ($n->tipe === 'success' ? 'text-success' : 'text-info')) }}">
                </i>
            </div>

            {{-- Konten --}}
            <div style="flex:1; min-width:0">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="font-weight-{{ $n->sudahDibaca() ? 'normal text-muted' : 'bold' }}"
                         style="font-size:14px">
                        {{ $n->judul }}
                    </div>
                    <small class="text-muted ml-3 text-nowrap" style="font-size:11px">
                        {{ $n->waktu }}
                    </small>
                </div>
                <div class="text-muted mt-1" style="font-size:13px">
                    {{ $n->pesan }}
                </div>
                <div class="mt-1" style="font-size:11px;color:#aaa">
                    {{ $n->created_at->format('d F Y, H:i') }}
                </div>
            </div>

            {{-- Aksi --}}
            <div class="d-flex flex-column align-items-end" style="gap:4px;flex-shrink:0">
                @if(!$n->sudahDibaca())
                <a href="{{ route('notifikasi.baca', $n) }}"
                   class="btn btn-xs btn-outline-primary" title="Tandai dibaca">
                    <i class="fas fa-check"></i>
                </a>
                @endif
                <form action="{{ route('notifikasi.destroy', $n) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-xs btn-outline-danger" title="Hapus">
                        <i class="fas fa-times"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center text-muted py-5">
            <i class="far fa-bell-slash fa-3x d-block mb-3"></i>
            <p class="mb-0">Tidak ada notifikasi.</p>
        </div>
        @endforelse
    </div>

    @if($notifikasi->hasPages())
    <div class="card-footer">
        {{ $notifikasi->links() }}
        <span class="float-right text-muted small">
            Total: {{ $notifikasi->total() }} notifikasi
        </span>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
function bacaSemua() {
    fetch('{{ route("notifikasi.baca-semua") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(() => location.reload());
}
</script>
@endpush
