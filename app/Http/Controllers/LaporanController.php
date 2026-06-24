<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kalibrasi;
use App\Models\Peminjaman;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PeminjamanExport;
use App\Exports\AlatExport;
use App\Exports\KalibrasiExport;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    // ---------- Halaman laporan ----------

    public function peminjaman(Request $request)
    {
        abort_unless(Auth::user()->canManage(), 403);
        $query = Peminjaman::with(['detail.alat', 'peminjam', 'disetujuiOleh']);

        $this->applyDateFilter($query, $request);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $peminjaman = $query->latest()->get();

        $summary = [
            'total'        => $peminjaman->count(),
            'dipinjam'     => $peminjaman->where('status', 'dipinjam')->count(),
            'dikembalikan' => $peminjaman->where('status', 'dikembalikan')->count(),
            'terlambat'    => $peminjaman->filter(fn($p) => $p->isTerlambat())->count(),
        ];

        return view('laporan.peminjaman', compact('peminjaman', 'summary'));
    }

    public function alat(Request $request)
    {
        abort_unless(Auth::user()->canManage(), 403);
        $alat = Alat::with('kategori')
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('kategori_id'), fn($q) => $q->where('kategori_id', $request->kategori_id))
            ->get();

        $summary = [
            'total'    => $alat->count(),
            'tersedia' => $alat->where('status', 'tersedia')->count(),
            'dipinjam' => $alat->where('status', 'dipinjam')->count(),
            'rusak'    => $alat->where('status', 'rusak')->count(),
            'servis'   => $alat->where('status', 'servis')->count(),
        ];

        return view('laporan.alat', compact('alat', 'summary'));
    }

    public function kalibrasi(Request $request)
    {
       abort_unless(Auth::user()->canManage(), 403);
        $query = Kalibrasi::with(['alat.kategori', 'dilakukanOleh']);

        $this->applyDateFilter($query, $request, 'tgl_kalibrasi');

        if ($request->filled('hasil')) {
            $query->where('hasil', $request->hasil);
        }

        $kalibrasi = $query->orderByDesc('tgl_kalibrasi')->get();

        $summary = [
            'total'           => $kalibrasi->count(),
            'lulus'           => $kalibrasi->where('hasil', 'lulus')->count(),
            'tidak_lulus'     => $kalibrasi->where('hasil', 'tidak_lulus')->count(),
            'perlu_perbaikan' => $kalibrasi->where('hasil', 'perlu_perbaikan')->count(),
            'total_biaya'     => $kalibrasi->sum('biaya'),
        ];

        return view('laporan.kalibrasi', compact('kalibrasi', 'summary'));
    }

    // ---------- Export PDF ----------

    public function exportPdfPeminjaman(Request $request)
    {
        abort_unless(Auth::user()->canManage(), 403);
        $query = Peminjaman::with(['detail.alat', 'peminjam']);
        $this->applyDateFilter($query, $request);
        $peminjaman = $query->latest()->get();

        $pdf = Pdf::loadView('laporan.pdf.peminjaman', compact('peminjaman'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-peminjaman-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportPdfAlat(Request $request)
    {
        abort_unless(Auth::user()->canManage(), 403);
        $alat = Alat::with('kategori')
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->get();

        $pdf = Pdf::loadView('laporan.pdf.alat', compact('alat'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('laporan-alat-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportPdfKalibrasi(Request $request)
    {
        abort_unless(Auth::user()->canManage(), 403); 
        $kalibrasi = Kalibrasi::with(['alat', 'dilakukanOleh'])
            ->orderByDesc('tgl_kalibrasi')->get();

        $pdf = Pdf::loadView('laporan.pdf.kalibrasi', compact('kalibrasi'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-kalibrasi-' . now()->format('Y-m-d') . '.pdf');
    }

    // ---------- Export Excel ----------

    public function exportExcelPeminjaman(Request $request)
    {
        return Excel::download(new PeminjamanExport($request), 'laporan-peminjaman-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportExcelAlat(Request $request)
    {
        return Excel::download(new AlatExport($request), 'laporan-alat-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportExcelKalibrasi(Request $request)
    {
        return Excel::download(new KalibrasiExport($request), 'laporan-kalibrasi-' . now()->format('Y-m-d') . '.xlsx');
    }

    // ---------- Private ----------

    private function applyDateFilter($query, Request $request, string $column = 'tgl_pinjam'): void
    {
        if ($request->filled('tgl_dari')) {
            $query->whereDate($column, '>=', $request->tgl_dari);
        }
        if ($request->filled('tgl_sampai')) {
            $query->whereDate($column, '<=', $request->tgl_sampai);
        }
    }
}
