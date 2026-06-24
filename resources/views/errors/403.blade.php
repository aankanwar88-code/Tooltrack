<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Akses Ditolak | ToolTrack</title>
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
            max-width: 520px;
            width: 100%;
            animation: fadeUp .4s cubic-bezier(.4,0,.2,1);
        }

        @keyframes fadeUp {
            from { opacity:0; transform: translateY(20px); }
            to   { opacity:1; transform: translateY(0); }
        }

        .error-icon-wrap {
            width: 100px; height: 100px;
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 28px;
        }

        .error-icon-wrap i {
            font-size: 40px;
            color: #dc2626;
        }

        .error-code {
            font-size: 80px;
            font-weight: 800;
            color: #dc2626;
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
            margin-bottom: 32px;
        }

        .error-desc strong { color: #374151; }

        .btn-group-error {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
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

        .brand {
            margin-top: 36px;
            padding-top: 24px;
            border-top: 1px solid #f3f4f6;
            display: flex; align-items: center;
            justify-content: center; gap: 8px;
            color: #9ca3af; font-size: 13px;
        }

        .brand i { color: #6b7280; }

        .role-info {
            display: inline-flex; align-items: center; gap: 6px;
            background: #fef3c7;
            border: 1px solid #fde68a;
            color: #92400e;
            font-size: 12px; font-weight: 600;
            padding: 5px 12px; border-radius: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="error-container">

    {{-- Icon --}}
    <div class="error-icon-wrap">
        <i class="fas fa-shield-alt"></i>
    </div>

    {{-- Kode & Judul --}}
    <div class="error-code">403</div>
    <div class="error-title">Akses Ditolak</div>

    {{-- Role info --}}
    @auth
    <div class="role-info">
        <i class="fas fa-user-tag"></i>
        Role Anda: {{ ucfirst(Auth::user()->role) }}
    </div>
    @endauth

    {{-- Deskripsi --}}
    <div class="error-desc">
        Anda tidak memiliki izin untuk mengakses halaman ini.<br>
        Halaman ini hanya dapat diakses oleh <strong>pengguna dengan role tertentu</strong>.<br><br>
        Jika Anda merasa ini adalah kesalahan, hubungi administrator sistem.
    </div>

    {{-- Tombol --}}
    <div class="btn-group-error">
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}"
           class="btn-error btn-outline-error">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <a href="{{ route('dashboard') }}" class="btn-error btn-primary-error">
            <i class="fas fa-home"></i> Ke Dashboard
        </a>
    </div>

    {{-- Brand --}}
    <div class="brand">
        <i class="fas fa-wrench"></i>
        <span><strong>ToolTrack</strong> — Sistem Manajemen Alat Kerja</span>
    </div>

</div>

</body>
</html>
