<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasi = Notifikasi::untukUser(Auth::id())
            ->latest()
            ->paginate(20);

        $jumlahBelumDibaca = Notifikasi::untukUser(Auth::id())
            ->belumDibaca()
            ->count();

        return view('notifikasi.index', compact('notifikasi', 'jumlahBelumDibaca'));
    }

    public function baca(Notifikasi $notifikasi)
    {
        abort_unless($notifikasi->user_id === Auth::id(), 403);
        $notifikasi->tandaiDibaca();

        return $notifikasi->url
            ? redirect($notifikasi->url)
            : redirect()->route('notifikasi.index');
    }

    public function bacaSemua()
    {
        Notifikasi::untukUser(Auth::id())
            ->belumDibaca()
            ->update(['dibaca_at' => now()]);

        return response()->json(['status' => 'ok']);
    }

    public function destroy(Notifikasi $notifikasi)
    {
        abort_unless($notifikasi->user_id === Auth::id(), 403);
        $notifikasi->delete();

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    public function hapusSemua()
    {
        Notifikasi::untukUser(Auth::id())->delete();

        return back()->with('success', 'Semua notifikasi berhasil dihapus.');
    }
}
