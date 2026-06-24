<?php

namespace App\Console\Commands;

use App\Mail\PengingatKalibrasi;
use App\Models\Kalibrasi;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class KirimPengingatKalibrasi extends Command
{
    protected $signature   = 'kalibrasi:kirim-pengingat';
    protected $description = 'Kirim email & notifikasi pengingat kalibrasi alat';

    public function handle(): int
    {
        $today = now()->toDateString();
        $batas = now()->addDays(30)->toDateString();

        // Ambil kalibrasi terbaru per alat yang terlambat
        $terlambat = Kalibrasi::with(['alat.kategori'])
            ->where('tgl_kalibrasi_berikutnya', '<', $today)
            ->whereIn('id', fn($q) =>
                $q->selectRaw('MAX(id)')
                  ->from('kalibrasi')
                  ->groupBy('alat_id')
            )->get();

        // Ambil kalibrasi terbaru per alat yang jatuh tempo
        $jatuhTempo = Kalibrasi::with(['alat.kategori'])
            ->whereBetween('tgl_kalibrasi_berikutnya', [$today, $batas])
            ->whereIn('id', fn($q) =>
                $q->selectRaw('MAX(id)')
                  ->from('kalibrasi')
                  ->groupBy('alat_id')
            )->get();

        $total = $terlambat->count() + $jatuhTempo->count();

        if ($total === 0) {
            $this->info('Tidak ada alat yang perlu diingatkan.');
            return self::SUCCESS;
        }

        // Ambil semua admin & petugas aktif
        $penerima = User::whereIn('role', ['admin', 'petugas'])
            ->where('is_active', true)
            ->get();

        foreach ($penerima as $user) {

            // ── Buat notifikasi in-app ──────────────
            foreach ($terlambat as $kal) {
                Notifikasi::kirim(
                    userId:     $user->id,
                    judul:      'Kalibrasi Terlambat: ' . $kal->alat->nama,
                    pesan:      'Jadwal kalibrasi sudah melewati ' .
                                $kal->tgl_kalibrasi_berikutnya->diffInDays(now()) .
                                ' hari. Segera lakukan kalibrasi.',
                    tipe:       'danger',
                    icon:       'fas fa-exclamation-circle',
                    url:        route('kalibrasi.index'),
                    notifiable: $kal,
                );
            }

            foreach ($jatuhTempo as $kal) {
                Notifikasi::kirim(
                    userId:     $user->id,
                    judul:      'Kalibrasi Jatuh Tempo: ' . $kal->alat->nama,
                    pesan:      'Jadwal kalibrasi dalam ' .
                                now()->diffInDays($kal->tgl_kalibrasi_berikutnya) .
                                ' hari lagi (' .
                                $kal->tgl_kalibrasi_berikutnya->format('d M Y') . ').',
                    tipe:       'warning',
                    icon:       'fas fa-clock',
                    url:        route('kalibrasi.index'),
                    notifiable: $kal,
                );
            }           
        }

        $this->info("Pengingat dikirim ke {$penerima->count()} user.");
        $this->info("  Terlambat  : {$terlambat->count()} alat");
        $this->info("  Jatuh tempo: {$jatuhTempo->count()} alat");

        return self::SUCCESS;
    }
}
