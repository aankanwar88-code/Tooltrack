{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — ToolTrack</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">

    {{-- AdminLTE 3 + Bootstrap 4 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">

    {{-- Select2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

    {{-- SweetAlert2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        body { font-family: 'Source Sans 3', sans-serif; }
        .brand-link .brand-text { font-weight: 700; font-size: 1.1rem; }
        .nav-sidebar .nav-link p { font-size: .875rem; }
        .card-header .card-title { font-weight: 700; }
        .table th { font-size: .8rem; text-transform: uppercase; letter-spacing: .05em; }
        .badge { font-size: .78rem; }
        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active,
        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link:hover { background: rgba(255,255,255,.12); }
    </style>

    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    {{-- ── Navbar ──────────────────────────────────── --}}
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            {{-- Notifikasi kalibrasi jatuh tempo --}}
            @php
                $notifKal = \App\Models\Kalibrasi::where('tgl_kalibrasi_berikutnya', '<=', now()->addDays(30)->toDateString())
                    ->whereIn('id', fn($s) => $s->selectRaw('MAX(id)')->from('kalibrasi')->groupBy('alat_id'))
                    ->count();
            @endphp
            @if($notifKal > 0)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('kalibrasi.index', ['jatuh_tempo' => 1]) }}">
                    <i class="fas fa-bell text-warning"></i>
                    <span class="badge badge-warning navbar-badge">{{ $notifKal }}</span>
                </a>
            </li>
            @endif

        @php
            $notifs = Auth::user()->notifikasiBelumDibaca()->take(8)->get();
            $jumlahNotif = $notifs->count();
        @endphp

        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                @if($jumlahNotif > 0)
                <span class="badge badge-danger navbar-badge">
                    {{ $jumlahNotif > 99 ? '99+' : $jumlahNotif }}
                </span>
                @endif
            </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header
                        d-flex justify-content-between align-items-center">
                    <span>{{ $jumlahNotif }} Notifikasi Baru</span>
                    @if($jumlahNotif > 0)
                    <a href="#" onclick="bacaSemua(event)"
                        class="text-sm text-muted">Baca Semua</a>
                    @endif
                    </span>
                <div class="dropdown-divider"></div>

                @forelse($notifs as $notif)
                <a href="{{ route('notifikasi.baca', $notif) }}"
                    class="dropdown-item">
                    <div class="d-flex align-items-start" style="gap:10px">
                <i class="{{ $notif->icon }}
                   text-{{ $notif->tipe === 'danger' ? 'danger' :
                           ($notif->tipe === 'warning' ? 'warning' : 'info') }}
                   mt-1" style="width:14px"></i>
                <div style="flex:1;min-width:0">
                    <div class="font-weight-bold text-sm
                                text-truncate">{{ $notif->judul }}</div>
                    <small class="text-muted">
                        {{ Str::limit($notif->pesan, 60) }}
                    </small>
                    <div style="font-size:10px;color:#aaa;margin-top:2px">
                        {{ $notif->waktu }}
                    </div>
                </div>
            </div>
        </a>
        <div class="dropdown-divider"></div>
        @empty
        <div class="dropdown-item text-center text-muted py-3">
            <i class="far fa-bell-slash d-block fa-2x mb-2"></i>
            Tidak ada notifikasi baru
        </div>
        @endforelse

        <div class="dropdown-divider"></div>
        <a href="{{ route('notifikasi.index') }}"
           class="dropdown-item dropdown-footer">
            Lihat Semua Notifikasi
        </a>
    </div>
</li>
           

            {{-- User dropdown --}}
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="fas fa-user-circle mr-1"></i>
                    {{ Auth::user()->name }}
                    <span class="ml-1">{!! Auth::user()->role_badge !!}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="#" class="dropdown-item"><i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Profil</a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i> Keluar
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </nav>

    {{-- ── Sidebar ─────────────────────────────────── --}}
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="{{ route('dashboard') }}" class="brand-link">
            <i class="fas fa-wrench ml-3 mr-2 text-warning"></i>
            <span class="brand-text font-weight-bold">ToolTrack</span>
        </a>

        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <i class="fas fa-user-circle fa-2x text-white ml-1"></i>
                </div>
                <div class="info">
                    <a href="#" class="d-block text-white">{{ Auth::user()->name }}</a>
                    <small class="text-muted text-capitalize">{{ Auth::user()->role }}</small>
                </div>
            </div>

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="{{ route('notifikasi.index') }}"
                        class="nav-link {{ request()->routeIs('notifikasi.*') ? 'active' : '' }}">
                    <i class="nav-icon far fa-bell"></i>
            <p>
            Notifikasi
            @if(Auth::user()->jumlah_notifikasi > 0)
            <span class="badge badge-danger right">
                {{ Auth::user()->jumlah_notifikasi }}
            </span>
            @endif
        </p>
    </a>
</li>

                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-header">INVENTARIS</li>

                    <li class="nav-item">
                        <a href="{{ route('alat.import.form') }}" class="nav-link {{ request()->routeIs('alat.import.*') ? 'active':'' }}">
                            <i class="nav-icon fas fa-file-excel"></i>
                            <p>Import Excel</p>
                        </a>
                        <a href="{{ route('alat.index') }}" class="nav-link {{ request()->routeIs('alat.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tools"></i>
                            <p>Alat Kerja</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('peminjaman.index') }}" class="nav-link {{ request()->routeIs('peminjaman.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <p>Peminjaman
                                @php $menunggu = \App\Models\Peminjaman::where('status','menunggu')->count() @endphp
                                @if($menunggu > 0 && Auth::user()->canManage())
                                    <span class="badge badge-warning right">{{ $menunggu }}</span>
                                @endif
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('kalibrasi.index') }}" class="nav-link {{ request()->routeIs('kalibrasi.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-ruler-combined"></i>
                            <p>Kalibrasi</p>
                        </a>
                    </li>

                   @if(Auth::user()->canManage())
				<li class="nav-header">LAPORAN</li>
				<li class="nav-item {{ request()->routeIs('laporan.*') ? 'menu-open' : '' }}">
    					<a href="#" class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
        					<i class="nav-icon fas fa-chart-bar"></i>
        					<p>Laporan <i class="right fas fa-angle-left"></i></p>
    					</a>
    					<ul class="nav nav-treeview">
        					<li class="nav-item">
            					<a href="{{ route('laporan.peminjaman') }}"
               						class="nav-link {{ request()->routeIs('laporan.peminjaman') ? 'active' : '' }}">
                							<i class="far fa-circle nav-icon"></i><p>Peminjaman</p>
            					</a>
       					 </li>
        					<li class="nav-item">
            					<a href="{{ route('laporan.alat') }}"
               						class="nav-link {{ request()->routeIs('laporan.alat') ? 'active' : '' }}">
                							<i class="far fa-circle nav-icon"></i><p>Alat Kerja</p>
            					</a>
        					</li>
        					<li class="nav-item">
        	    					<a href="{{ route('laporan.kalibrasi') }}"
               						class="nav-link {{ request()->routeIs('laporan.kalibrasi') ? 'active' : '' }}">
                						<i class="far fa-circle nav-icon"></i><p>Kalibrasi</p>
            					</a>
        					</li>
   	 				</ul>
				</li>
			    @endif
                    @if(Auth::user()->isAdmin())
                    <li class="nav-header">ADMINISTRASI</li>
                    <li class="nav-item">
                        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>Manajemen User</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('activity-log.index') }}"
                            class="nav-link {{ request()->routeIs('activity-log.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Activity Log</p>
                        </a>
                    </li>
                    @endif

                </ul>
            </nav>
        </div>
    </aside>

    {{-- ── Content ──────────────────────────────────── --}}
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">@yield('page-title')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            @yield('breadcrumb')
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">

                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    {{-- ── Footer ───────────────────────────────────── --}}
    <footer class="main-footer">
        <strong>ToolTrack</strong> &copy; {{ date('Y') }} — Sistem Manajemen Alat Kerja.
        <div class="float-right d-none d-sm-inline-block">
            <b>Laravel</b> 12 + <b>AdminLTE</b> 3
        </div>
    </footer>

</div>{{-- /wrapper --}}

{{-- JS --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script>
    // DataTable default
    $(function () {
        $('[data-table]').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            responsive: true,
            pageLength: 15,
        });

        // Select2
        $('.select2').select2({ theme: 'bootstrap4', width: '100%' });

        // Auto-close alert
        setTimeout(() => $('.alert').alert('close'), 5000);

        // SweetAlert delete confirm
        $(document).on('submit', 'form[data-confirm]', function (e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Yakin?',
                text: $(this).data('confirm') || 'Data akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
            }).then((result) => { if (result.isConfirmed) form.submit(); });
        });
    });
</script>

<script>
function bacaSemua(e) {
    e.preventDefault();
    fetch('{{ route("notifikasi.baca-semua") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(() => location.reload());
}
</script>
@stack('scripts')
</body>
</html>
