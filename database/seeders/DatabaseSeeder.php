<?php

namespace Database\Seeders;

use App\Models\KategoriAlat;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Admin
        User::create([
            'name'       => 'Administrator',
            'email'      => 'admin@tooltrack.id',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'no_induk'   => 'ADM-001',
            'departemen' => 'IT',
        ]);

        // Akun Petugas
        User::create([
            'name'       => 'Petugas Gudang',
            'email'      => 'petugas@tooltrack.id',
            'password'   => Hash::make('password'),
            'role'       => 'petugas',
            'no_induk'   => 'PTG-001',
            'departemen' => 'Gudang',
        ]);

        // Akun Peminjam
        User::create([
            'name'       => 'Budi Santoso',
            'email'      => 'budi@tooltrack.id',
            'password'   => Hash::make('password'),
            'role'       => 'peminjam',
            'no_induk'   => 'EMP-001',
            'departemen' => 'Teknik',
        ]);

        // Kategori Alat
        $kategori = [
            ['nama' => 'Alat Listrik',  'kode' => 'ELK'],
            ['nama' => 'Alat Tangan',   'kode' => 'HND'],
            ['nama' => 'Alat Ukur',     'kode' => 'UKR'],
            ['nama' => 'Alat Las',      'kode' => 'LAS'],
            ['nama' => 'Alat Potong',   'kode' => 'PTG'],
            ['nama' => 'Lainnya',       'kode' => 'LNY'],
        ];

        foreach ($kategori as $kat) {
            KategoriAlat::create($kat);
        }
    }
}
