<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
       $query = Peminjaman::with(['peminjam', 'detail.alat']);

        if (Auth::user()->isPeminjam()) {
            $query->where('peminjam_id', Auth::id());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('peminjam', fn($q2) => $q2->where('name', 'like', "%{$request->search}%"))
                  ->orWhere('no_pinjam', 'like', "%{$request->search}%")
                  ->orWhereHas('detail.alat', fn($q2) => $q2->where('nama', 'like', "%{$request->search}%"));
            });
        }

        $peminjaman = $query->latest()->paginate(15)->withQueryString();

        return view('peminjaman.index', compact('peminjaman'));
    }

    public function create()
    {
        $alat = Alat::tersedia()->with('kategori')->orderBy('nama')->get();
        return view('peminjaman.create', compact('alat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'alat_ids'            => 'required|array|min:1|max:10',
            'alat_ids.*'          => 'exists:alat,id',
            'tgl_pinjam'          => 'required|date|after_or_equal:today',
            'tgl_kembali_rencana' => 'required|date|after:tgl_pinjam',
            'keperluan'           => 'nullable|string|max:500',
        ], [
            'alat_ids.required' => 'Pilih minimal 1 alat.',
            'alat_ids.max'      => 'Maksimal 10 alat per transaksi.',
        ]);

        // Cek semua alat tersedia
        $alatList = Alat::whereIn('id', $request->alat_ids)->get();
        $tidakTersedia = $alatList->where('status', '!=', 'tersedia');

        if ($tidakTersedia->isNotEmpty()) {
            $namaAlat = $tidakTersedia->pluck('nama')->join(', ');
            return back()->with('error', "Alat berikut tidak tersedia: {$namaAlat}.")->withInput();
        }

        DB::transaction(function () use ($request, $alatList) {
            $status = Auth::user()->canManage() ? 'dipinjam' : 'menunggu';

            // Buat header peminjaman
            $peminjaman = Peminjaman::create([
                'no_pinjam'          => Peminjaman::generateNoPinjam(),
                'peminjam_id'        => Auth::id(),
                'disetujui_oleh'     => Auth::user()->canManage() ? Auth::id() : null,
                'tgl_pinjam'         => $request->tgl_pinjam,
                'tgl_kembali_rencana'=> $request->tgl_kembali_rencana,
                'status'             => $status,
                'keperluan'          => $request->keperluan,
            ]);

            // Buat detail per alat & ubah status alat
            foreach ($alatList as $alat) {
                PeminjamanDetail::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id'       => $alat->id,
                ]);

                if (Auth::user()->canManage()) {
                    $alat->update(['status' => 'dipinjam']);
                }
            }
        });

        return redirect()->route('peminjaman.index')
            ->with('success', 'Peminjaman ' . count($request->alat_ids) . ' alat berhasil dibuat.');
    }

    public function show(Peminjaman $peminjaman)
    {
        $this->authorizeAccess($peminjaman);
        $peminjaman->load(['detail.alat.kategori', 'peminjam', 'disetujuiOleh']);
        return view('peminjaman.show', compact('peminjaman'));
    }

    public function approve(Peminjaman $peminjaman)
    {
        abort_unless(Auth::user()->canManage(), 403);

        if ($peminjaman->status !== 'menunggu') {
            return back()->with('error', 'Status peminjaman tidak valid.');
        }

        DB::transaction(function () use ($peminjaman) {
            $peminjaman->update([
                'status'         => 'dipinjam',
                'disetujui_oleh' => Auth::id(),
            ]);

            // Ubah status semua alat
            foreach ($peminjaman->detail as $d) {
                $d->alat->update(['status' => 'dipinjam']);
            }
        });

        return back()->with('success', 'Peminjaman disetujui. ' . $peminjaman->jumlahAlat() . ' alat berstatus Dipinjam.');
    }

    public function reject(Request $request, Peminjaman $peminjaman)
    {
        abort_unless(Auth::user()->canManage(), 403);
        $request->validate(['catatan_petugas' => 'nullable|string|max:500']);

        $peminjaman->update([
            'status'          => 'ditolak',
            'catatan_petugas' => $request->catatan_petugas,
            'disetujui_oleh'  => Auth::id(),
        ]);

        return back()->with('success', 'Peminjaman ditolak.');
    }

    public function kembali(Request $request, Peminjaman $peminjaman)
    {
        abort_unless(Auth::user()->canManage(), 403);

        if ($peminjaman->status !== 'dipinjam') {
            return back()->with('error', 'Status peminjaman tidak valid untuk dikembalikan.');
        }

        $request->validate([
            'detail'              => 'required|array',
            'detail.*.alat_id'    => 'required|exists:alat,id',
            'detail.*.kondisi'    => 'required|string|max:500',
            'detail.*.status_alat'=> 'required|in:tersedia,rusak,servis',
        ]);

        DB::transaction(function () use ($request, $peminjaman) {
            foreach ($request->detail as $item) {
                $d = $peminjaman->detail()->where('alat_id', $item['alat_id'])->first();
                if (!$d) continue;

                $d->update([
                    'kondisi_kembali'    => $item['kondisi'],
                    'status_alat_kembali'=> $item['status_alat'],
                    'tgl_kembali_aktual' => now()->toDateString(),
                ]);

                $d->alat->update(['status' => $item['status_alat']]);
            }

            // Jika semua alat sudah dikembalikan → ubah status peminjaman
            if ($peminjaman->semuaDikembalikan()) {
                $peminjaman->update(['status' => 'dikembalikan']);
            }
        });

        $sisa = $peminjaman->detail()->whereNull('tgl_kembali_aktual')->count();
        $msg  = 'Pengembalian berhasil dicatat.';
        if ($sisa > 0) $msg .= " Masih ada {$sisa} alat belum dikembalikan.";

        return back()->with('success', $msg);
    }

    private function authorizeAccess(Peminjaman $peminjaman): void
    {
        if (Auth::user()->isPeminjam() && $peminjaman->peminjam_id !== Auth::id()) {
            abort(403);
        }
    }
}