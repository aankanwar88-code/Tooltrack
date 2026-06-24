<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kalibrasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KalibrasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Kalibrasi::with(['alat.kategori', 'dilakukanOleh']);

        if ($request->filled('search')) {
            $query->where('no_kalibrasi', 'like', "%{$request->search}%")
                  ->orWhereHas('alat', fn($q) => $q->where('nama', 'like', "%{$request->search}%"));
        }

        if ($request->filled('hasil')) {
            $query->where('hasil', $request->hasil);
        }

        // Filter kalibrasi jatuh tempo bulan ini
        if ($request->filled('jatuh_tempo')) {
            $query->where('tgl_kalibrasi_berikutnya', '<=', now()->addDays(30)->toDateString());
        }

        $kalibrasi = $query->orderByDesc('tgl_kalibrasi')->paginate(15)->withQueryString();

        // Alat yang akan jatuh tempo (untuk alert)
        $alatJatuhTempo = Alat::whereHas('kalibrasi', function ($q) {
            $q->where('tgl_kalibrasi_berikutnya', '<=', now()->addDays(30)->toDateString())
              ->whereIn('id', function ($sub) {
                  $sub->selectRaw('MAX(id)')
                      ->from('kalibrasi')
                      ->groupBy('alat_id');
              });
        })->count();

        return view('kalibrasi.index', compact('kalibrasi', 'alatJatuhTempo'));
    }

    public function create()
    {
        abort_unless(Auth::user()->canManage(), 403);
        $alat = Alat::with('kategori')->orderBy('nama')->get();
        return view('kalibrasi.create', compact('alat'));
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->canManage(), 403);

        $validated = $request->validate([
            'alat_id'                  => 'required|exists:alat,id',
            'tgl_kalibrasi'            => 'required|date',
            'tgl_kalibrasi_berikutnya' => 'required|date|after:tgl_kalibrasi',
            'hasil'                    => 'required|in:lulus,tidak_lulus,perlu_perbaikan',
            'lembaga_kalibrasi'        => 'nullable|string|max:255',
            'no_sertifikat'            => 'nullable|string|max:100',
            'biaya'                    => 'nullable|numeric|min:0',
            'keterangan'               => 'nullable|string',
            'dokumen'                  => 'nullable|file|mimes:pdf,jpg,png|max:5120',
        ]);

        $validated['no_kalibrasi']  = Kalibrasi::generateNoKalibrasi();
        $validated['dilakukan_oleh'] = Auth::id();

        if ($request->hasFile('dokumen')) {
            $validated['dokumen'] = $request->file('dokumen')->store('kalibrasi', 'public');
        }

        Kalibrasi::create($validated);
        // Kirim notifikasi in-app jika tidak lulus
if (in_array($validated['hasil'], ['tidak_lulus', 'perlu_perbaikan'])) {
    $penerima = User::whereIn('role', ['admin', 'pertugas'])
        ->where('is_active', true)->get();

    foreach ($penerima as $u) {
        Notifikasi::kirim(
            userId:     $u->id,
            judul:      'Kalibrasi Tidak Lulus: ' . $alat->nama,
            pesan:      'Hasil kalibrasi ' . $validated['no_kalibrasi'] .
                        ' adalah ' . $validated['hasil'] .
                        '. Status alat diubah ke Servis.',
            tipe:       'danger',
            icon:       'fas fa-times-circle',
            url:        route('kalibrasi.show', $kalibrasi),
            notifiable: $kalibrasi,
        );
    }
}

        // Jika tidak lulus → set status alat ke servis
        if ($validated['hasil'] !== 'lulus') {
            Alat::find($validated['alat_id'])->update(['status' => 'servis']);
        }

        return redirect()->route('kalibrasi.index')
            ->with('success', 'Data kalibrasi berhasil disimpan.');
    }

    public function show(Kalibrasi $kalibrasi)
    {
        $kalibrasi->load(['alat.kategori', 'dilakukanOleh']);
        return view('kalibrasi.show', compact('kalibrasi'));
    }

    public function edit(Kalibrasi $kalibrasi)
    {
        abort_unless(Auth::user()->canManage(), 403);
        $alat = Alat::with('kategori')->orderBy('nama')->get();
        return view('kalibrasi.edit', compact('kalibrasi', 'alat'));
    }

    public function update(Request $request, Kalibrasi $kalibrasi)
    {
        abort_unless(Auth::user()->canManage(), 403);

        $validated = $request->validate([
            'alat_id'                  => 'required|exists:alat,id',
            'tgl_kalibrasi'            => 'required|date',
            'tgl_kalibrasi_berikutnya' => 'required|date|after:tgl_kalibrasi',
            'hasil'                    => 'required|in:lulus,tidak_lulus,perlu_perbaikan',
            'lembaga_kalibrasi'        => 'nullable|string|max:255',
            'no_sertifikat'            => 'nullable|string|max:100',
            'biaya'                    => 'nullable|numeric|min:0',
            'keterangan'               => 'nullable|string',
            'dokumen'                  => 'nullable|file|mimes:pdf,jpg,png|max:5120',
        ]);

        if ($request->hasFile('dokumen')) {
            if ($kalibrasi->dokumen) Storage::disk('public')->delete($kalibrasi->dokumen);
            $validated['dokumen'] = $request->file('dokumen')->store('kalibrasi', 'public');
        }

        $kalibrasi->update($validated);
               // Kirim notifikasi in-app jika tidak lulus
if (in_array($validated['hasil'], ['tidak_lulus', 'perlu_perbaikan'])) {
    $penerima = User::whereIn('role', ['admin', 'pertugas'])
        ->where('is_active', true)->get();

    foreach ($penerima as $u) {
        Notifikasi::kirim(
            userId:     $u->id,
            judul:      'Kalibrasi Tidak Lulus: ' . $alat->nama,
            pesan:      'Hasil kalibrasi ' . $validated['no_kalibrasi'] .
                        ' adalah ' . $validated['hasil'] .
                        '. Status alat diubah ke Servis.',
            tipe:       'danger',
            icon:       'fas fa-times-circle',
            url:        route('kalibrasi.show', $kalibrasi),
            notifiable: $kalibrasi,
        );
    }
}


        return redirect()->route('kalibrasi.index')
            ->with('success', 'Data kalibrasi berhasil diperbarui.');
    }

    public function destroy(Kalibrasi $kalibrasi)
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        if ($kalibrasi->dokumen) Storage::disk('public')->delete($kalibrasi->dokumen);
        $kalibrasi->delete();

        return redirect()->route('kalibrasi.index')
            ->with('success', 'Data kalibrasi berhasil dihapus.');
    }
}
