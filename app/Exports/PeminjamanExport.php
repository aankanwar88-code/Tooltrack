<?php
// app/Exports/PeminjamanExport.php

namespace App\Exports;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PeminjamanExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private Request $request) {}

    public function collection()
    {
        $query = Peminjaman::with(['detail.alat', 'peminjam']);

        if ($this->request->filled('tgl_dari')) {
            $query->whereDate('tgl_pinjam', '>=', $this->request->tgl_dari);
        }
        if ($this->request->filled('tgl_sampai')) {
            $query->whereDate('tgl_pinjam', '<=', $this->request->tgl_sampai);
        }
        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return ['No', 'No Pinjam', 'Alat', 'Kode Alat', 'Peminjam', 'Tgl Pinjam', 'Tgl Kembali Rencana', 'Tgl Kembali Aktual', 'Status', 'Keperluan'];
    }

    public function map($row): array
    {
static $i = 0;
    $i++;

    $alatNama = $row->detail->pluck('alat.nama')->implode(', ');
    $alatKode = $row->detail->pluck('alat.kode')->implode(', ');

    return [
        $i,
        $row->no_pinjam,
        $alatNama ?: '—',
        $alatKode ?: '—',
        $row->peminjam->name ?? '—',
        $row->tgl_pinjam->format('d/m/Y'),
        $row->tgl_kembali_rencana->format('d/m/Y'),
        $row->tgl_kembali_aktual?->format('d/m/Y') ?? '—',
        ucfirst($row->status),
        $row->keperluan ?? '—',
    ];
}
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'D9E1F2']]],
        ];
    }
}
