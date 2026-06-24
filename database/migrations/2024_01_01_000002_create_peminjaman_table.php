<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('no_pinjam')->unique();
            $table->foreignId('alat_id')->constrained('alat')->restrictOnDelete();
            $table->foreignId('peminjam_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tgl_pinjam');
            $table->date('tgl_kembali_rencana');
            $table->date('tgl_kembali_aktual')->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'dipinjam', 'dikembalikan', 'ditolak'])->default('menunggu');
            $table->text('keperluan')->nullable();
            $table->text('catatan_petugas')->nullable();
            $table->text('kondisi_kembali')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
