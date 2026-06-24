<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');           // created, updated, deleted, dll
            $table->string('model_type');       // App\Models\Alat, Peminjaman, dst
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('description');      // kalimat deskriptif
            $table->json('old_values')->nullable();  // nilai sebelum diubah
            $table->json('new_values')->nullable();  // nilai sesudah diubah
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
