<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update data lama: status 'disetujui' → 'dipinjam'
        DB::table('peminjaman')
            ->where('status', 'disetujui')
            ->update(['status' => 'dipinjam']);

        // Update enum kolom status (MySQL)
        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN status
            ENUM('menunggu','dipinjam','dikembalikan','ditolak')
            NOT NULL DEFAULT 'menunggu'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN status
            ENUM('menunggu','disetujui','dipinjam','dikembalikan','ditolak')
            NOT NULL DEFAULT 'menunggu'");
    }
};
