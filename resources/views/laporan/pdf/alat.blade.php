{{-- resources/views/laporan/pdf/alat.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Laporan Alat Kerja</title>
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: Arial, sans-serif; font-size: 10px; color: #222; }
    .header { text-align:center; margin-bottom:14px; border-bottom:2px solid #2c3e50; padding-bottom:10px; }
    .header h1 { font-size:15px; font-weight:700; color:#2c3e50; }
    .header p { font-size:9px; color:#666; margin-top:3px; }
    table { width:100%; border-collapse:collapse; margin-top:8px; }
    thead tr { background:#2c3e50; color:#fff; }
    th { padding:6px 6px; font-size:9px; font-weight:600; text-align:left; }
    td { padding:4px 6px; border-bottom:1px solid #e8e8e8; }
    tr:nth-child(even) td { background:#f5f6f7; }
    .badge { padding:2px 6px; border-radius:8px; font-size:8px; font-weight:700; display:inline-block; }
    .b-ok { background:#d4edda; color:#155724; }
    .b-warn { background:#fff3cd; color:#856404; }
    .b-danger { background:#f8d7da; color:#721c24; }
    .b-info { background:#d1ecf1; color:#0c5460; }
    .summary-row { display:flex; gap:10px; margin-bottom:14px; }
    .sum { flex:1; border:1px solid #ddd; border-radius:4px; padding:8px; text-align:center; }
    .sum .n { font-size:20px; font-weight:700; }
    .sum .l { font-size:8px; color:#666; }
    .footer { margin-top:16px; font-size:8px; color:#aaa; text-align:right; }
</style>
</head>
<body>

<div class="header">
    <h1>LAPORAN INVENTARIS ALAT KERJA</h1>
    <p>Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>
</div>

<div class="summary-row">
    <div class="sum"><div class="n">{{ $alat->count() }}</div><div class="l">Total Alat</div></div>
    <div class="sum"><div class="n" style="color:#27ae60">{{ $alat->where('status','tersedia')->count() }}</div><div class="l">Tersedia</div></div>
    <div class="sum"><div class="n" style="color:#e67e22">{{ $alat->where('status','dipinjam')->count() }}</div><div class="l">Dipinjam</div></div>
    <div class="sum"><div class="n" style="color:#e74c3c">{{ $alat->where('status','rusak')->count() }}</div><div class="l">Rusak</div></div>
    <div class="sum"><div class="n" style="color:#3498db">{{ $alat->where('status','servis')->count() }}</div><div class="l">Servis</div></div>
</div>

{{-- Tombol Export --}}
<div class="mb-3 d-flex" style="gap:.5rem">
    <a href="{{ route('laporan.alat.pdf', request()->query()) }}" 
       class="btn btn-danger btn-sm" target="_blank">
        <i class="fas fa-file-pdf mr-1"></i> Export PDF
    </a>

    <a href="{{ route('laporan.alat.excel', request()->query()) }}" 
       class="btn btn-success btn-sm">
        <i class="fas fa-file-excel mr-1"></i> Export Excel
    </a>
</div>
<table>
    <thead>
        <tr>
            <th>No</th><th>Kode</th><th>Nama Alat</th><th>Kategori</th>
            <th>Merk</th><th>No Seri</th><th>Lokasi</th><th>Status</th><th>Tgl Beli</th>
        </tr>
    </thead>
    <tbody>
        @foreach($alat as $i => $a)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $a->kode }}</td>
            <td><strong>{{ $a->nama }}</strong></td>
            <td>{{ $a->kategori->nama }}</td>
            <td>{{ $a->merk ?? '—' }}</td>
            <td>{{ $a->no_seri ?? '—' }}</td>
            <td>{{ $a->lokasi ?? '—' }}</td>
            <td>
                @php $cls = match($a->status){ 'tersedia'=>'b-ok','dipinjam'=>'b-warn','rusak'=>'b-danger','servis'=>'b-info',default=>'b-info' }; @endphp
                <span class="badge {{ $cls }}">{{ ucfirst($a->status) }}</span>
            </td>
            <td>{{ $a->tgl_beli?->format('d/m/Y') ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">ToolTrack — Sistem Manajemen Alat Kerja</div>
</body>
</html>
