<?php
// database/seeders/KategoriAlatSeeder.php

namespace Database\Seeders;

use App\Models\KategoriAlat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class KategoriAlatSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            ['kode' => 'ELK', 'nama' => 'Alat Listrik',  'deskripsi' => 'Bor, gerinda, sander, gergaji listrik, dll.'],
            ['kode' => 'HND', 'nama' => 'Alat Tangan',   'deskripsi' => 'Kunci pas, obeng, palu, tang, kunci torsi, dll.'],
            ['kode' => 'UKR', 'nama' => 'Alat Ukur',     'deskripsi' => 'Multimeter, jangka sorong, waterpass, laser level, dll.'],
            ['kode' => 'LAS', 'nama' => 'Alat Las',      'deskripsi' => 'Mesin las MIG, TIG, elektroda, cutting torch, dll.'],
            ['kode' => 'PTG', 'nama' => 'Alat Potong',   'deskripsi' => 'Gerinda potong, mesin potong besi, gergaji, dll.'],
            ['kode' => 'LNY', 'nama' => 'Lainnya',       'deskripsi' => 'Peralatan yang tidak termasuk kategori di atas.'],
        ];

        foreach ($kategori as $kat) {
            KategoriAlat::firstOrCreate(['kode' => $kat['kode']], $kat);
        }

        // Salin template Excel ke storage/app/templates/
        $this->copyTemplate();
    }

    private function copyTemplate(): void
    {
        $templateDir = storage_path('app/templates');

        if (!is_dir($templateDir)) {
            mkdir($templateDir, 0755, true);
        }

        $src  = base_path('resources/excel/template-import-alat.xlsx');
        $dest = $templateDir . '/template-import-alat.xlsx';

        if (file_exists($src) && !file_exists($dest)) {
            copy($src, $dest);
        }
    }
}
