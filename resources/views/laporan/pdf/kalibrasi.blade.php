<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Laporan Kalibrasi</title>
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: Arial, sans-serif; font-size:10px; color:#222; }
    .header { text-align:center; margin-bottom:14px; border-bottom:2px solid #2c3e50; padding-bottom:10px; }
    .header h1 { font-size:15px; font-weight:700; color:#2c3e50; }
    .header p  { font-size:9px; color:#666; margin-top:3px; }
    .summary { display:flex; gap:10px; margin-bottom:14px; }
    .sum { flex:1; border:1px solid #ddd; border-radius:4px; padding:8px; text-align:center; }
    .sum .n { font-size:20px; font-weight:700; }
    .sum .l { font-size:8px; color:#666; }
    table { width:100%; border-collapse:collapse; }
    thead tr { background:#2c3e50; color:#fff; }
    th { padding:5px 6px; font-size:9px; font-weight:600; text-align:left; }
    td { padding:4px 6px; border-bottom:1px solid #e8e8e8; vertical-align:middle; }
    tr:nth-child(even) td { background:#f5f6f7; }
    tr.warning td { background:#fff9e6; }
    .badge { padding:2px 6px; border-radius:8px; font-size:8px; font-weight:700; display:inline-block; }
    .b-ok   { background:#d4edda; color:#155724; }
    .b-ng   { background:#f8d7da; color:#721c24; }
    .b-warn { background:#fff3cd; color:#856404; }
    .footer { margin-top:16px; font-size:8px; color:#aaa; text-align:right; }
</style>
</head>
<body>

<div class="header">
    <h1>LAPORAN KALIBRASI ALAT KERJA</h1>
    <p>Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB &bull; PT. Makino Indonesia</p>
</div>

<div class="summary">
    <div class="sum">
        <div class="n">{{ $kalibrasi->count() }}</div>
        <div class="l">Total Kalibrasi</div>
    </div>
    <div class="sum">
        <div class="n" style="color:#27ae60">
            {{ $kalibrasi->where('hasil','lulus')->count() }}
        </div>
        <div class="l">Lulus</div>
    </div>
    <div class="sum">
        <div class="n" style="color:#e74c3c">
            {{ $kalibrasi->where('hasil','tidak_lulus')->count() }}
        </div>
        <div class="l">Tidak Lulus</div>
    </div>
    <div class="sum">
        <div class="n" style="color:#e67e22">
            {{ $kalibrasi->where('hasil','perlu_perbaikan')->count() }}
        </div>
        <div class="l">Perlu Perbaikan</div>
    </div>
    <div class="sum">
        <div class="n" style="color:#2c3e50; font-size:14px">
            Rp {{ number_format($kalibrasi->sum('biaya'),0,',','.') }}
        </div>
        <div class="l">Total Biaya</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>No Kalibrasi</th>
            <th>Alat</th>
            <th>Tgl Kalibrasi</th>
            <th>Tgl Berikutnya</th>
            <th>Hasil</th>
            <th>Lembaga</th>
            <th>Biaya (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($kalibrasi as $i => $k)
        <tr class="{{ $k->isJatuhTempo() ? 'warning' : '' }}">
            <td>{{ $i+1 }}</td>
            <td>{{ $k->no_kalibrasi }}</td>
            <td>
                <strong>{{ $k->alat->nama ?? '—' }}</strong><br>
                <small>{{ $k->alat->kode ?? '' }}</small>
            </td>
            <td>{{ $k->tgl_kalibrasi->format('d/m/Y') }}</td>
            <td>{{ $k->tgl_kalibrasi_berikutnya->format('d/m/Y') }}</td>
            <td>
                @php
                    $cls = match($k->hasil) {
                        'lulus'           => 'b-ok',
                        'tidak_lulus'     => 'b-ng',
                        'perlu_perbaikan' => 'b-warn',
                        default           => ''
                    };
                    $label = match($k->hasil) {
                        'lulus'           => 'Lulus',
                        'tidak_lulus'     => 'Tidak Lulus',
                        'perlu_perbaikan' => 'Perlu Perbaikan',
                        default           => $k->hasil
                    };
                @endphp
                <span class="badge {{ $cls }}">{{ $label }}</span>
            </td>
            <td>{{ $k->lembaga_kalibrasi ?? '—' }}</td>
            <td>{{ $k->biaya ? number_format($k->biaya,0,',','.') : '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    ToolTrack — Sistem Manajemen Alat Kerja &bull; PT. Makino Indonesia
</div>

</body>
</html>