<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kalibrasi', function (Blueprint $table) {
            $table->id();
            $table->string('no_kalibrasi')->unique();
            $table->foreignId('alat_id')->constrained('alat')->restrictOnDelete();
            $table->foreignId('dilakukan_oleh')->constrained('users')->restrictOnDelete();
            $table->date('tgl_kalibrasi');
            $table->date('tgl_kalibrasi_berikutnya');
            $table->enum('hasil', ['lulus', 'tidak_lulus', 'perlu_perbaikan'])->default('lulus');
            $table->string('lembaga_kalibrasi')->nullable();
            $table->string('no_sertifikat')->nullable();
            $table->decimal('biaya', 15, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('dokumen')->nullable(); // file sertifikat
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kalibrasi');
    }
};
