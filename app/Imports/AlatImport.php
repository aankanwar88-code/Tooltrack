<?php
// app/Imports/AlatImport.php

namespace App\Imports;

use App\Models\Alat;
use App\Models\KategoriAlat;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class AlatImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public array $errors  = [];
    public array $preview = [];
    public int   $success = 0;
    private bool $saveMode;

    // Heading row index (baris ke-4 adalah header, rows 1-3 adalah judul/instruksi)
    public function headingRow(): int { return 4; }

    public function __construct(bool $saveMode = false)
    {
        $this->saveMode = $saveMode;
    }

    public function collection(Collection $rows)
    {
        // Ambil semua kategori sekali saja
        $kategoriMap = KategoriAlat::pluck('id', 'kode')->toArray();

        foreach ($rows as $index => $row) {
            $rowNum = $index + 5; // offset karena header ada di baris 4

            // Skip baris contoh (italic hint)
            $namaAlat = trim((string) ($row['nama_alat'] ?? ''));
            if (empty($namaAlat)) continue;

            // Mapping kolom (sesuai heading row template)
            $data = [
                'nama'        => $namaAlat,
                'kode_kategori' => strtoupper(trim((string) ($row['kode_kategori'] ?? ''))),
                'status'      => strtolower(trim((string) ($row['status'] ?? 'tersedia'))),
                'merk'        => trim((string) ($row['merk'] ?? '')) ?: null,
                'no_seri'     => trim((string) ($row['no_seri'] ?? '')) ?: null,
                'lokasi'      => trim((string) ($row['lokasi'] ?? '')) ?: null,
                'tgl_beli'    => $this->parseDate($row['tanggal_beliyyyy_mm_dd'] ?? $row['tanggal_beli'] ?? null),
                'harga_beli'  => $this->parseNumber($row['harga_belirp'] ?? $row['harga_beli'] ?? null),
                'keterangan'  => trim((string) ($row['keterangan'] ?? '')) ?: null,
            ];

            // Validasi
            $rowErrors = $this->validateRow($data, $rowNum, $kategoriMap);

            if (!empty($rowErrors)) {
                $this->errors[] = ['baris' => $rowNum, 'pesan' => $rowErrors];
                continue;
            }

            // Resolve kategori_id
            $kategoriId = $kategoriMap[$data['kode_kategori']];

            if ($this->saveMode) {
                // Simpan ke database
                $kodeAlat = Alat::generateKode($data['kode_kategori']);
                Alat::create([
                    'kode'        => $kodeAlat,
                    'nama'        => $data['nama'],
                    'kategori_id' => $kategoriId,
                    'status'      => $data['status'],
                    'merk'        => $data['merk'],
                    'no_seri'     => $data['no_seri'],
                    'lokasi'      => $data['lokasi'],
                    'tgl_beli'    => $data['tgl_beli'],
                    'harga_beli'  => $data['harga_beli'],
                    'keterangan'  => $data['keterangan'],
                ]);
                $this->success++;
            } else {
                // Mode preview — hanya kumpulkan data
                $this->preview[] = array_merge($data, [
                    'kategori_nama' => KategoriAlat::find($kategoriId)->nama ?? '-',
                ]);
            }
        }
    }

    private function validateRow(array $data, int $rowNum, array $kategoriMap): array
    {
        $errors = [];

        if (empty($data['nama'])) {
            $errors[] = 'Nama Alat wajib diisi.';
        }

        if (empty($data['kode_kategori'])) {
            $errors[] = 'Kode Kategori wajib diisi.';
        } elseif (!array_key_exists($data['kode_kategori'], $kategoriMap)) {
            $errors[] = "Kode Kategori '{$data['kode_kategori']}' tidak ditemukan. Gunakan: " . implode(', ', array_keys($kategoriMap));
        }

        if (!in_array($data['status'], ['tersedia', 'rusak', 'servis'])) {
            $errors[] = "Status '{$data['status']}' tidak valid. Pilih: tersedia, rusak, atau servis.";
        }

        if ($data['tgl_beli'] === false) {
            $errors[] = 'Format Tanggal Beli tidak valid. Gunakan: YYYY-MM-DD.';
        }

        return $errors;
    }

    private function parseDate($value): string|null|false
    {
        if (empty($value)) return null;

        // Excel numeric date
        if (is_numeric($value)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $date->format('Y-m-d');
            } catch (\Exception) {
                return false;
            }
        }

        $str = trim((string) $value);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $str)) return $str;
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $str)) {
            return \Carbon\Carbon::createFromFormat('d/m/Y', $str)->format('Y-m-d');
        }

        return false;
    }

    private function parseNumber($value): float|null
    {
        if (empty($value)) return null;
        $clean = preg_replace('/[^0-9.]/', '', (string) $value);
        return is_numeric($clean) ? (float) $clean : null;
    }

    public function hasErrors(): bool { return !empty($this->errors); }
}
