{{-- resources/views/laporan/pdf/peminjaman.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Laporan Peminjaman Alat</title>
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 10px; color: #222; }
    .header { text-align:center; margin-bottom:16px; border-bottom:2px solid #2c3e50; padding-bottom:10px; }
    .header h1 { font-size:16px; font-weight:700; color:#2c3e50; margin-bottom:4px; }
    .header p { font-size:10px; color:#666; }
    .meta { display:flex; justify-content:space-between; margin-bottom:12px; font-size:9px; color:#666; }
    table { width:100%; border-collapse:collapse; margin-top:8px; }
    thead tr { background:#2c3e50; color:#fff; }
    th { padding:6px 8px; text-align:left; font-size:9px; font-weight:600; letter-spacing:.03em; }
    td { padding:5px 8px; border-bottom:1px solid #e0e0e0; vertical-align:middle; }
    tr:nth-child(even) td { background:#f8f9fa; }
    .badge { display:inline-block; padding:2px 7px; border-radius:10px; font-size:8px; font-weight:700; }
    .badge-success { background:#d4edda; color:#155724; }
    .badge-warning { background:#fff3cd; color:#856404; }
    .badge-danger  { background:#f8d7da; color:#721c24; }
    .badge-info    { background:#d1ecf1; color:#0c5460; }
    .badge-secondary { background:#e2e3e5; color:#383d41; }
    .summary { display:flex; gap:12px; margin-bottom:14px; }
    .summary-item { flex:1; background:#f0f4f8; border-left:3px solid #2c3e50; padding:8px 10px; border-radius:4px; }
    .summary-item .val { font-size:18px; font-weight:700; color:#2c3e50; }
    .summary-item .lbl { font-size:8px; color:#666; }
    .footer { margin-top:20px; font-size:9px; color:#999; text-align:right; border-top:1px solid #ddd; padding-top:6px; }
    .terlambat { color:#c0392b; font-weight:700; }
</style>
</head>
<body>

<div class="header">
    <h1>LAPORAN PEMINJAMAN ALAT KERJA</h1>
    <p>Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>
</div>

<div class="summary">
    <div class="summary-item">
        <div class="val">{{ $peminjaman->count() }}</div>
        <div class="lbl">Total Transaksi</div>
    </div>
    <div class="summary-item">
        <div class="val">{{ $peminjaman->where('status','dipinjam')->count() }}</div>
        <div class="lbl">Masih Dipinjam</div>
    </div>
    <div class="summary-item">
        <div class="val">{{ $peminjaman->where('status','dikembalikan')->count() }}</div>
        <div class="lbl">Dikembalikan</div>
    </div>
    <div class="summary-item">
        <div class="val">{{ $peminjaman->filter(fn($p)=>$p->isTerlambat())->count() }}</div>
        <div class="lbl">Terlambat</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>No Pinjam</th>
            <th>Alat</th>
            <th>Peminjam</th>
            <th>Tgl Pinjam</th>
            <th>Tgl Kembali</th>
            <th>Tgl Kembali Aktual</th>
            <th>Status</th>
        </tr>
    </thead>
<tbody>
    @foreach($peminjaman as $i => $p)
    <tr>
        <td>{{ $i + 1 }}</td>

        <td>{{ $p->no_pinjam }}</td>

        <td>
            @if($p->detail->count())
                @foreach($p->detail as $d)
                    <div>
                        • {{ $d->alat?->nama ?? '—' }}
                        <span style="color:#777">
                            ({{ $d->alat?->kode ?? '-' }})
                        </span>
                    </div>
                @endforeach
            @else
                —
            @endif
        </td>

        <td>{{ $p->peminjam->name ?? '—' }}</td>

        <td>{{ $p->tgl_pinjam->format('d/m/Y') }}</td>

        <td class="{{ $p->isTerlambat() ? 'terlambat' : '' }}">
            {{ $p->tgl_kembali_rencana->format('d/m/Y') }}
        </td>

        <td>
            {{ $p->tgl_kembali_aktual?->format('d/m/Y') ?? '—' }}
        </td>

        <td>
            @php
                $cls = match($p->status) {
                    'dipinjam'     => 'badge-warning',
                    'dikembalikan' => 'badge-success',
                    'ditolak'      => 'badge-danger',
                    'disetujui'    => 'badge-info',
                    default        => 'badge-secondary',
                };
            @endphp
            <span class="badge {{ $cls }}">
                {{ ucfirst($p->status) }}
            </span>
        </td>
    </tr>
    @endforeach
</tbody>
</table>

<div class="footer">
    ToolTrack — Sistem Manajemen Alat Kerja &bull; Halaman {PAGE_NUM} dari {PAGE_COUNT}
</div>

</body>
</html>
