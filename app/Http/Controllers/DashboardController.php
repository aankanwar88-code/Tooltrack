<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kalibrasi;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Statistik utama
        $stats = [
            'total_alat'       => Alat::count(),
            'alat_tersedia'    => Alat::where('status', 'tersedia')->count(),
            'alat_dipinjam'    => Alat::where('status', 'dipinjam')->count(),
            'alat_rusak_servis'=> Alat::whereIn('status', ['rusak', 'servis'])->count(),
        ];

        // Peminjaman aktif (terlambat)
        $peminjamanTerlambat = Peminjaman::with(['detail.alat', 'peminjam'])
            ->where('status', 'dipinjam')
            ->where('tgl_kembali_rencana', '<', now()->toDateString())
            ->latest()
            ->take(5)
            ->get();

        // Kalibrasi jatuh tempo (30 hari ke depan)
        $kalibrasiJatuhTempo = Kalibrasi::with('alat')
            ->where('tgl_kalibrasi_berikutnya', '<=', now()->addDays(30)->toDateString())
            ->whereIn('id', function ($sub) {
                $sub->selectRaw('MAX(id)')->from('kalibrasi')->groupBy('alat_id');
            })
            ->orderBy('tgl_kalibrasi_berikutnya')
            ->take(5)
            ->get();

        // Peminjaman menunggu persetujuan
        $menungguPersetujuan = Peminjaman::with(['detail.alat', 'peminjam'])
            ->where('status', 'menunggu')
            ->latest()
            ->take(5)
            ->get();

        // Peminjaman terbaru (untuk semua / milik sendiri)
        $peminjamanTerbaru = Peminjaman::with(['detail.alat', 'peminjam'])
            ->when($user->isPeminjam(), fn($q) => $q->where('peminjam_id', $user->id))
            ->latest()
            ->take(8)
            ->get();

        // Statistik user (khusus admin)
        $totalUser = $user->isAdmin() ? User::count() : null;

        return view('dashboard', compact(
            'stats',
            'peminjamanTerlambat',
            'kalibrasiJatuhTempo',
            'menungguPersetujuan',
            'peminjamanTerbaru',
            'totalUser'
        ));
    }
}
