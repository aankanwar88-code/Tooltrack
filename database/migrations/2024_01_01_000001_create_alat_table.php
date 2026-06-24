<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_alat', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode', 10)->unique();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('alat', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->foreignId('kategori_id')->constrained('kategori_alat')->restrictOnDelete();
            $table->enum('status', ['tersedia', 'dipinjam', 'rusak', 'servis'])->default('tersedia');
            $table->string('lokasi')->nullable();
            $table->string('merk')->nullable();
            $table->string('no_seri')->nullable();
            $table->date('tgl_beli')->nullable();
            $table->decimal('harga_beli', 15, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('foto')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alat');
        Schema::dropIfExists('kategori_alat');
    }
};
