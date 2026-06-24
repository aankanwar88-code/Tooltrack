<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul');
            $table->text('pesan');
            $table->enum('tipe', ['info', 'warning', 'danger', 'success'])->default('info');
            $table->string('icon')->default('fas fa-bell');
            $table->string('url')->nullable();          // link tujuan saat diklik
            $table->morphs('notifiable');               // relasi ke model terkait (Kalibrasi/Alat)
            $table->timestamp('dibaca_at')->nullable(); // null = belum dibaca
            $table->timestamps();

            $table->index(['user_id', 'dibaca_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
