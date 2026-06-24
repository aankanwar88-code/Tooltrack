<?php
// app/Exports/AlatExport.php

namespace App\Exports;

use App\Models\Alat;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AlatExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private Request $request) {}

    public function collection()
    {
        return Alat::with('kategori')
            ->when($this->request->filled('status'), fn($q) => $q->where('status', $this->request->status))
            ->when($this->request->filled('kategori_id'), fn($q) => $q->where('kategori_id', $this->request->kategori_id))
            ->orderBy('kode')
            ->get();
    }

    public function headings(): array
    {
        return ['No', 'Kode', 'Nama Alat', 'Kategori', 'Merk', 'No Seri', 'Status', 'Lokasi', 'Tgl Beli', 'Harga Beli', 'Keterangan'];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;
        return [
            $i,
            $row->kode,
            $row->nama,
            $row->kategori->nama,
            $row->merk ?? '—',
            $row->no_seri ?? '—',
            ucfirst($row->status),
            $row->lokasi ?? '—',
            $row->tgl_beli?->format('d/m/Y') ?? '—',
            $row->harga_beli ? number_format($row->harga_beli, 0, ',', '.') : '—',
            $row->keterangan ?? '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'D9E1F2']]],
        ];
    }
}
