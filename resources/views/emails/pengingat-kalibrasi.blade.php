<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pengingat Kalibrasi — ToolTrack</title>
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: Arial, sans-serif; background:#F3F4F6; color:#1F2937; }
    .wrapper { max-width:620px; margin:30px auto; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,.08); }
    .header { background:#1F2937; padding:28px 32px; }
    .header .logo { color:#F9FAFB; font-size:20px; font-weight:700; }
    .header .logo span { color:#9CA3AF; font-size:13px; font-weight:400; display:block; margin-top:3px; }
    .hero { background:linear-gradient(135deg,#1F4788,#2E75B6); padding:28px 32px; text-align:center; }
    .hero h1 { color:#fff; font-size:22px; font-weight:700; margin-bottom:6px; }
    .hero p  { color:rgba(255,255,255,.8); font-size:13px; }
    .body { padding:28px 32px; }
    .greeting { font-size:15px; margin-bottom:18px; }
    .greeting strong { color:#1F4788; }
    .summary-row { display:flex; gap:12px; margin-bottom:24px; }
    .summary-card { flex:1; border-radius:8px; padding:16px; text-align:center; }
    .summary-card.danger  { background:#FEF2F2; border:1px solid #FECACA; }
    .summary-card.warning { background:#FFFBEB; border:1px solid #FDE68A; }
    .summary-card .num { font-size:32px; font-weight:700; line-height:1; }
    .summary-card.danger  .num { color:#DC2626; }
    .summary-card.warning .num { color:#D97706; }
    .summary-card .lbl { font-size:12px; color:#6B7280; margin-top:4px; }
    .section-title {
        font-size:13px; font-weight:700; letter-spacing:.05em;
        text-transform:uppercase; color:#6B7280;
        border-bottom:2px solid #E5E7EB;
        padding-bottom:6px; margin-bottom:12px;
    }
    .alat-card {
        border:1px solid #E5E7EB; border-radius:8px;
        padding:12px 16px; margin-bottom:10px;
        display:flex; justify-content:space-between; align-items:flex-start;
    }
    .alat-card.danger  { border-left:4px solid #DC2626; }
    .alat-card.warning { border-left:4px solid #D97706; }
    .alat-info .nama { font-weight:700; font-size:14px; color:#111827; }
    .alat-info .meta { font-size:12px; color:#6B7280; margin-top:3px; }
    .badge {
        display:inline-block; padding:3px 10px; border-radius:20px;
        font-size:11px; font-weight:700; white-space:nowrap;
    }
    .badge-danger  { background:#FEE2E2; color:#991B1B; }
    .badge-warning { background:#FEF3C7; color:#92400E; }
    .cta { text-align:center; margin:28px 0 8px; }
    .cta a {
        display:inline-block; background:#1F4788; color:#fff;
        padding:12px 32px; border-radius:8px; font-size:14px;
        font-weight:700; text-decoration:none; letter-spacing:.02em;
    }
    .cta a:hover { background:#2E75B6; }
    .footer {
        background:#F9FAFB; border-top:1px solid #E5E7EB;
        padding:18px 32px; text-align:center;
        font-size:11px; color:#9CA3AF;
    }
    .footer a { color:#6B7280; }
</style>
</head>
<body>
<div class="wrapper">

    {{-- Header --}}
    <div class="header">
        <div class="logo">
            ⚙ ToolTrack
            <span>Sistem Manajemen Alat Kerja</span>
        </div>
    </div>

    {{-- Hero --}}
    <div class="hero">
        <h1>🔔 Pengingat Jadwal Kalibrasi</h1>
        <p>{{ now()->format('F Y') }} — Laporan Bulanan Otomatis</p>
    </div>

    {{-- Body --}}
    <div class="body">

        <p class="greeting">
            Halo, <strong>{{ $recipientName }}</strong>! <br>
            Berikut adalah rangkuman alat kerja yang memerlukan perhatian terkait jadwal kalibrasi.
        </p>

        {{-- Summary --}}
        <div class="summary-row">
            <div class="summary-card danger">
                <div class="num">{{ $alatTerlambat->count() }}</div>
                <div class="lbl">Terlambat Kalibrasi</div>
            </div>
            <div class="summary-card warning">
                <div class="num">{{ $alatJatuhTempo->count() }}</div>
                <div class="lbl">Jatuh Tempo &le; 30 Hari</div>
            </div>
        </div>

        {{-- Alat terlambat --}}
        @if($alatTerlambat->count() > 0)
        <div class="section-title">🚨 Alat Terlambat Kalibrasi</div>
        @foreach($alatTerlambat as $k)
        <div class="alat-card danger">
            <div class="alat-info">
                <div class="nama">{{ $k->alat->nama }}</div>
                <div class="meta">
                    {{ $k->alat->kode }} &bull; {{ $k->alat->kategori->nama }}
                    @if($k->alat->lokasi) &bull; {{ $k->alat->lokasi }} @endif
                </div>
                <div class="meta" style="margin-top:4px">
                    Jadwal: <strong>{{ $k->tgl_kalibrasi_berikutnya->format('d F Y') }}</strong>
                    &bull; Lembaga: {{ $k->lembaga_kalibrasi ?? '—' }}
                </div>
            </div>
            <span class="badge badge-danger">
                {{ $k->tgl_kalibrasi_berikutnya->diffInDays(now()) }} hari terlambat
            </span>
        </div>
        @endforeach
        <br>
        @endif

        {{-- Alat jatuh tempo --}}
        @if($alatJatuhTempo->count() > 0)
        <div class="section-title">⚠️ Jatuh Tempo dalam 30 Hari</div>
        @foreach($alatJatuhTempo as $k)
        <div class="alat-card warning">
            <div class="alat-info">
                <div class="nama">{{ $k->alat->nama }}</div>
                <div class="meta">
                    {{ $k->alat->kode }} &bull; {{ $k->alat->kategori->nama }}
                    @if($k->alat->lokasi) &bull; {{ $k->alat->lokasi }} @endif
                </div>
                <div class="meta" style="margin-top:4px">
                    Jadwal: <strong>{{ $k->tgl_kalibrasi_berikutnya->format('d F Y') }}</strong>
                    &bull; Lembaga: {{ $k->lembaga_kalibrasi ?? '—' }}
                </div>
            </div>
            <span class="badge badge-warning">
                {{ now()->diffInDays($k->tgl_kalibrasi_berikutnya) }} hari lagi
            </span>
        </div>
        @endforeach
        <br>
        @endif

        <div class="cta">
            <a href="{{ config('app.url') }}/kalibrasi?jatuh_tempo=1">
                Buka Halaman Kalibrasi →
            </a>
        </div>

        <p style="font-size:12px;color:#9CA3AF;text-align:center;margin-top:12px">
            Email ini dikirim otomatis oleh sistem ToolTrack setiap awal bulan.<br>
            Hanya admin dan petugas yang menerima email ini.
        </p>

    </div>

    {{-- Footer --}}
    <div class="footer">
        &copy; {{ date('Y') }} ToolTrack — Sistem Manajemen Alat Kerja<br>
        <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>
    </div>

</div>
</body>
</html>
