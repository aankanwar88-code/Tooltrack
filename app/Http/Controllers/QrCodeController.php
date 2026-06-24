<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    /**
     * Tampilkan QR Code satu alat (inline di halaman).
     */
    public function show(Alat $alat)
    {
        $url = route('alat.show', $alat);

        $qr = QrCode::format('svg')
            ->size(250)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($url);

        return view('alat.qrcode', compact('alat', 'qr', 'url'));
    }

    /**
     * Download QR Code sebagai file SVG.
     */
    public function download(Alat $alat)
    {
        $url = route('alat.show', $alat);

        $qr = QrCode::format('svg')
            ->size(400)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($url);

        return response($qr, 200, [
            'Content-Type'        => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="QR-' . $alat->kode . '.svg"',
        ]);
    }

    /**
     * Cetak QR Code banyak alat sekaligus.
     */
    public function cetakMassal(Request $request)
    {
        $request->validate([
            'alat_ids'   => 'required|array|min:1|max:50',
            'alat_ids.*' => 'exists:alat,id',
        ]);

        $alatList = Alat::with('kategori')
            ->whereIn('id', $request->alat_ids)
            ->get()
            ->map(function ($alat) {
                $url = route('alat.show', $alat);
                $qr  = QrCode::format('svg')
                    ->size(200)
                    ->margin(1)
                    ->errorCorrection('H')
                    ->generate($url);

                return ['alat' => $alat, 'qr' => $qr];
            });

        return view('alat.qrcode-massal', compact('alatList'));
    }
}
