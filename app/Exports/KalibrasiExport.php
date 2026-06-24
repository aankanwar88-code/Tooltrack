<?php
// app/Exports/KalibrasiExport.php

namespace App\Exports;

use App\Models\Kalibrasi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KalibrasiExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private Request $request) {}

    public function collection()
    {
        $query = Kalibrasi::with(['alat', 'dilakukanOleh']);

        if ($this->request->filled('tgl_dari')) {
            $query->whereDate('tgl_kalibrasi', '>=', $this->request->tgl_dari);
        }
        if ($this->request->filled('tgl_sampai')) {
            $query->whereDate('tgl_kalibrasi', '<=', $this->request->tgl_sampai);
        }
        if ($this->request->filled('hasil')) {
            $query->where('hasil', $this->request->hasil);
        }

        return $query->orderByDesc('tgl_kalibrasi')->get();
    }

    public function headings(): array
    {
        return [
            'No', 'No Kalibrasi', 'Alat', 'Kode Alat',
            'Tgl Kalibrasi', 'Tgl Berikutnya', 'Hasil',
            'Lembaga', 'No Sertifikat', 'Biaya (Rp)', 'Dilakukan Oleh', 'Keterangan',
        ];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;
        return [
            $i,
            $row->no_kalibrasi,
            $row->alat->nama,
            $row->alat->kode,
            $row->tgl_kalibrasi->format('d/m/Y'),
            $row->tgl_kalibrasi_berikutnya->format('d/m/Y'),
            match($row->hasil) {
                'lulus'           => 'Lulus',
                'tidak_lulus'     => 'Tidak Lulus',
                'perlu_perbaikan' => 'Perlu Perbaikan',
                default           => $row->hasil,
            },
            $row->lembaga_kalibrasi ?? '—',
            $row->no_sertifikat ?? '—',
            $row->biaya ? number_format($row->biaya, 0, ',', '.') : '—',
            $row->dilakukanOleh->name,
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
