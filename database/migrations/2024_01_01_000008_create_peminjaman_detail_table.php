<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel detail alat yang dipinjam per transaksi
        Schema::create('peminjaman_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjaman')->cascadeOnDelete();
            $table->foreignId('alat_id')->constrained('alat')->restrictOnDelete();
            $table->enum('status_alat_kembali', ['tersedia', 'rusak', 'servis'])->nullable();
            $table->text('kondisi_kembali')->nullable();
            $table->date('tgl_kembali_aktual')->nullable();
            $table->timestamps();

            $table->unique(['peminjaman_id', 'alat_id']); // 1 alat tidak boleh 2x di transaksi yg sama
        });

        // Hapus kolom yang dipindah ke detail dari tabel peminjaman
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropForeign(['alat_id']);
            $table->dropColumn(['alat_id', 'tgl_kembali_aktual', 'kondisi_kembali']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_detail');

        Schema::table('peminjaman', function (Blueprint $table) {
            $table->foreignId('alat_id')->nullable()->constrained('alat')->restrictOnDelete();
            $table->date('tgl_kembali_aktual')->nullable();
            $table->text('kondisi_kembali')->nullable();
        });
    }
};
