<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Halaman Tidak Ditemukan | ToolTrack</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .error-container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 40px rgba(0,0,0,.08);
            padding: 56px 48px;
            text-align: center;
            max-width: 560px;
            width: 100%;
            animation: fadeUp .4s cubic-bezier(.4,0,.2,1);
        }

        @keyframes fadeUp {
            from { opacity:0; transform: translateY(20px); }
            to   { opacity:1; transform: translateY(0); }
        }

        .error-icon-wrap {
            width: 100px; height: 100px;
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 28px;
            position: relative;
        }

        .error-icon-wrap i {
            font-size: 40px;
            color: #2563eb;
        }

        /* Animasi alat bergerak */
        .wrench-anim {
            position: absolute;
            top: -4px; right: -4px;
            width: 30px; height: 30px;
            background: #fff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,.12);
            animation: bounce 1.5s ease-in-out infinite;
        }

        .wrench-anim i { font-size: 14px; color: #f59e0b; }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-6px); }
        }

        .error-code {
            font-size: 80px;
            font-weight: 800;
            color: #2563eb;
            line-height: 1;
            letter-spacing: -3px;
            margin-bottom: 8px;
        }

        .error-title {
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
        }

        .error-desc {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.7;
            margin-bottom: 28px;
        }

        .url-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 16px;
            font-size: 12px;
            font-family: 'Courier New', monospace;
            color: #9ca3af;
            margin-bottom: 28px;
            word-break: break-all;
        }

        .btn-group-error {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 28px;
        }

        .btn-error {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 11px 22px;
            border-radius: 8px;
            font-size: 13px; font-weight: 600;
            text-decoration: none;
            border: none; cursor: pointer;
            transition: all .2s;
            font-family: 'Inter', sans-serif;
        }

        .btn-primary-error {
            background: #1f2937; color: #fff;
        }
        .btn-primary-error:hover {
            background: #374151; color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(31,41,55,.25);
        }

        .btn-outline-error {
            background: transparent; color: #6b7280;
            border: 1.5px solid #d1d5db;
        }
        .btn-outline-error:hover {
            border-color: #9ca3af; color: #374151;
        }

        .btn-blue-error {
            background: #eff6ff; color: #2563eb;
            border: 1.5px solid #bfdbfe;
        }
        .btn-blue-error:hover {
            background: #dbeafe;
        }

        /* Shortcut menu */
        .shortcuts {
            border-top: 1px solid #f3f4f6;
            padding-top: 24px;
        }

        .shortcuts-title {
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #9ca3af;
            font-weight: 600;
            margin-bottom: 14px;
        }

        .shortcut-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .shortcut-item {
            display: flex; flex-direction: column; align-items: center;
            gap: 5px; padding: 12px 8px;
            background: #f9fafb; border: 1px solid #e5e7eb;
            border-radius: 8px; text-decoration: none;
            color: #374151; font-size: 11px; font-weight: 600;
            transition: all .2s;
        }

        .shortcut-item:hover {
            background: #f0f9ff; border-color: #bae6fd;
            color: #0369a1; transform: translateY(-1px);
        }

        .shortcut-item i { font-size: 18px; color: #6b7280; }
        .shortcut-item:hover i { color: #0369a1; }

        .brand {
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid #f3f4f6;
            display: flex; align-items: center;
            justify-content: center; gap: 8px;
            color: #9ca3af; font-size: 13px;
        }

        .brand i { color: #6b7280; }
    </style>
</head>
<body>

<div class="error-container">

    {{-- Icon --}}
    <div class="error-icon-wrap">
        <i class="fas fa-map-signs"></i>
        <div class="wrench-anim">
            <i class="fas fa-wrench"></i>
        </div>
    </div>

    {{-- Kode & Judul --}}
    <div class="error-code">404</div>
    <div class="error-title">Halaman Tidak Ditemukan</div>

    {{-- Deskripsi --}}
    <div class="error-desc">
        Halaman yang Anda cari tidak tersedia atau mungkin sudah dipindahkan.<br>
        Periksa kembali URL yang Anda masukkan.
    </div>

    {{-- URL yang dicoba --}}
    <div class="url-box">
        <i class="fas fa-link" style="margin-right:6px"></i>
        {{ url()->current() }}
    </div>

    {{-- Tombol utama --}}
    <div class="btn-group-error">
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}"
           class="btn-error btn-outline-error">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <a href="{{ route('dashboard') }}" class="btn-error btn-primary-error">
            <i class="fas fa-home"></i> Dashboard
        </a>
    </div>

    {{-- Shortcut ke halaman utama --}}
    @auth
    <div class="shortcuts">
        <div class="shortcuts-title">Atau langsung ke:</div>
        <div class="shortcut-grid">
            <a href="{{ route('alat.index') }}" class="shortcut-item">
                <i class="fas fa-tools"></i> Alat Kerja
            </a>
            <a href="{{ route('peminjaman.index') }}" class="shortcut-item">
                <i class="fas fa-clipboard-list"></i> Peminjaman
            </a>
            <a href="{{ route('kalibrasi.index') }}" class="shortcut-item">
                <i class="fas fa-ruler-combined"></i> Kalibrasi
            </a>
            @if(Auth::user()->canManage())
            <a href="{{ route('laporan.peminjaman') }}" class="shortcut-item">
                <i class="fas fa-chart-bar"></i> Laporan
            </a>
            @endif
            <a href="{{ route('notifikasi.index') }}" class="shortcut-item">
                <i class="far fa-bell"></i> Notifikasi
            </a>
            @if(Auth::user()->isAdmin())
            <a href="{{ route('users.index') }}" class="shortcut-item">
                <i class="fas fa-users-cog"></i> User
            </a>
            @endif
        </div>
    </div>
    @endauth

    {{-- Brand --}}
    <div class="brand">
        <i class="fas fa-wrench"></i>
        <span><strong>ToolTrack</strong> — Sistem Manajemen Alat Kerja</span>
    </div>

</div>

</body>
</html>
