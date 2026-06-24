<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom role ke tabel users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
            $table->enum('role', ['admin', 'petugas', 'peminjam'])->default('peminjam')->after('email');
            $table->string('no_induk')->nullable()->after('role');
            $table->string('departemen')->nullable()->after('no_induk');
            $table->string('telepon')->nullable()->after('departemen');
            $table->boolean('is_active')->default(true)->after('telepon');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'no_induk', 'departemen', 'telepon', 'is_active']);
        });
    }
};
