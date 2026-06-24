<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\KategoriAlat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Imports\AlatImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;

class AlatController extends Controller
{
    public function index(Request $request)
    {
        $query = Alat::with('kategori');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('kode', 'like', "%{$request->search}%")
                  ->orWhere('merk', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $alat      = $query->latest()->paginate(15)->withQueryString();
        $kategori  = KategoriAlat::orderBy('nama')->get();

        return view('alat.index', compact('alat', 'kategori'));
    }

    public function create()
    {
        $kategori = KategoriAlat::orderBy('nama')->get();
        return view('alat.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'        => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_alat,id',
            'status'      => 'required|in:tersedia,dipinjam,rusak,servis',
            'lokasi'      => 'nullable|string|max:255',
            'merk'        => 'nullable|string|max:255',
            'no_seri'     => 'nullable|string|max:100',
            'tgl_beli'    => 'nullable|date',
            'harga_beli'  => 'nullable|numeric|min:0',
            'keterangan'  => 'nullable|string',
            'foto'        => 'nullable|image|max:2048',
        ]);

        $kategori        = KategoriAlat::findOrFail($request->kategori_id);
        $validated['kode'] = Alat::generateKode($kategori->kode);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('alat', 'public');
        }

        Alat::create($validated);

        return redirect()->route('alat.index')
            ->with('success', 'Alat berhasil ditambahkan.');
    }

    public function show(Alat $alat)
    {
        $alat->load(['kategori', 'peminjaman.peminjam', 'kalibrasi.dilakukanOleh']);
        return view('alat.show', compact('alat'));
    }

    public function edit(Alat $alat)
    {
        $kategori = KategoriAlat::orderBy('nama')->get();
        return view('alat.edit', compact('alat', 'kategori'));
    }

    public function update(Request $request, Alat $alat)
    {
        $validated = $request->validate([
            'nama'        => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_alat,id',
            'status'      => 'required|in:tersedia,dipinjam,rusak,servis',
            'lokasi'      => 'nullable|string|max:255',
            'merk'        => 'nullable|string|max:255',
            'no_seri'     => 'nullable|string|max:100',
            'tgl_beli'    => 'nullable|date',
            'harga_beli'  => 'nullable|numeric|min:0',
            'keterangan'  => 'nullable|string',
            'foto'        => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($alat->foto) Storage::disk('public')->delete($alat->foto);
            $validated['foto'] = $request->file('foto')->store('alat', 'public');
        }

        $alat->update($validated);

        return redirect()->route('alat.index')
            ->with('success', 'Alat berhasil diperbarui.');
    }

    public function destroy(Alat $alat)
    {
        if ($alat->status === 'dipinjam') {
            return back()->with('error', 'Alat sedang dipinjam, tidak dapat dihapus.');
        }

        $alat->delete();

        return redirect()->route('alat.index')
            ->with('success', 'Alat berhasil dihapus.');
    }
    
    public function importForm()
    {
        return view('alat.import');
    }
    public function importPreview(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls|max:5120',
        ], [
            'file_excel.required' => 'File Excel wajib dipilih.',
            'file_excel.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'file_excel.max'      => 'Ukuran file maksimal 5 MB.',
        ]);
 
        $import = new AlatImport(saveMode: false);
        Excel::import($import, $request->file('file_excel'));
 
        // Simpan preview ke session
        Session::put('import_preview', $import->preview);
        Session::put('import_errors', $import->errors);
 
        if (empty($import->preview) && empty($import->errors)) {
            return back()->with('error', 'File Excel tidak memiliki data. Pastikan Anda menggunakan template yang benar dan sudah mengisi data.');
        }
 
        return view('alat.import-preview', [
            'preview' => $import->preview,
            'errors'  => $import->errors,
        ]);
    }
 
    /**
     * Simpan data yang sudah dipreview ke database.
     */
    public function importSave(Request $request)
    {
        // Ambil file yang di-re-upload atau dari session untuk konfirmasi
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);
 
        $import = new AlatImport(saveMode: true);
        Excel::import($import, $request->file('file_excel'));
 
        Session::forget(['import_preview', 'import_errors']);
 
        if ($import->success === 0 && $import->hasErrors()) {
            return redirect()->route('alat.import.form')
                ->with('error', 'Import gagal. Semua baris memiliki kesalahan. Periksa kembali file Anda.')
                ->with('import_errors', $import->errors);
        }
 
        $msg = "Import berhasil! {$import->success} alat berhasil ditambahkan.";
        if ($import->hasErrors()) {
            $msg .= " " . count($import->errors) . " baris dilewati karena ada kesalahan.";
        }
 
        return redirect()->route('alat.index')->with('success', $msg);
    }
 
    /**
     * Download file template Excel.
     */
    public function downloadTemplate()
    {
        $path = storage_path('app/templates/template-import-alat.xlsx');
 
        if (!file_exists($path)) {
            return back()->with('error', 'File template tidak ditemukan. Hubungi administrator.');
        }
 
        return response()->download($path, 'template-import-alat.xlsx');
    }

}
