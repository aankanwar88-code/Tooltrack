<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeminjamanDetail extends Model
{
    protected $table = 'peminjaman_detail';

    protected $fillable = [
        'peminjaman_id', 'alat_id',
        'status_alat_kembali', 'kondisi_kembali', 'tgl_kembali_aktual',
    ];

    protected $casts = [
        'tgl_kembali_aktual' => 'date',
    ];

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class);
    }

    public function sudahDikembalikan(): bool
    {
        return $this->tgl_kembali_aktual !== null;
    }
}
