<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasActivityLog;

class Kalibrasi extends Model
{
    use HasActivityLog;
    protected $table = 'kalibrasi';

    protected $fillable = [
        'no_kalibrasi', 'alat_id', 'dilakukan_oleh',
        'tgl_kalibrasi', 'tgl_kalibrasi_berikutnya',
        'hasil', 'lembaga_kalibrasi', 'no_sertifikat',
        'biaya', 'keterangan', 'dokumen',
    ];

    protected $casts = [
        'tgl_kalibrasi'           => 'date',
        'tgl_kalibrasi_berikutnya' => 'date',
        'biaya'                   => 'decimal:2',
    ];

    // ---------- Relations ----------

    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class);
    }

    public function dilakukanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dilakukan_oleh');
    }

    // ---------- Helpers ----------

    public function getHasilBadgeAttribute(): string
    {
        return match ($this->hasil) {
            'lulus'            => '<span class="badge badge-success">Lulus</span>',
            'tidak_lulus'      => '<span class="badge badge-danger">Tidak Lulus</span>',
            'perlu_perbaikan'  => '<span class="badge badge-warning">Perlu Perbaikan</span>',
            default            => '<span class="badge badge-secondary">—</span>',
        };
    }

    public function isJatuhTempo(): bool
    {
        return $this->tgl_kalibrasi_berikutnya <= now()->addDays(30)->toDateString();
    }

    // ---------- Auto-generate no_kalibrasi ----------

    public static function generateNoKalibrasi(): string
    {
        $prefix = 'KAL-' . date('Ym') . '-';
        $last   = self::where('no_kalibrasi', 'like', "{$prefix}%")->orderByDesc('no_kalibrasi')->first();
        $next   = $last ? ((int) substr($last->no_kalibrasi, -4)) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
