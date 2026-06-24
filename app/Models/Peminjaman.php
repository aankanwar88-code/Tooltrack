<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $fillable = [
        'no_pinjam', 'peminjam_id', 'disetujui_oleh',
        'tgl_pinjam', 'tgl_kembali_rencana',
        'status', 'keperluan', 'catatan_petugas',
    ];

    protected $casts = [
        'tgl_pinjam'          => 'date',
        'tgl_kembali_rencana' => 'date',
    ];

    // ---------- Relations ----------

    public function peminjam(): BelongsTo
    {
        return $this->belongsTo(User::class, 'peminjam_id');
    }

    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(PeminjamanDetail::class);
    }

    // ---------- Helpers ----------

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'menunggu'     => '<span class="badge badge-secondary">Menunggu</span>',
            'dipinjam'     => '<span class="badge badge-warning">Dipinjam</span>',
            'dikembalikan' => '<span class="badge badge-success">Dikembalikan</span>',
            'ditolak'      => '<span class="badge badge-danger">Ditolak</span>',
            default        => '<span class="badge badge-secondary">—</span>',
        };
    }

    public function isTerlambat(): bool
    {
        if (in_array($this->status, ['dikembalikan', 'ditolak', 'menunggu'])) return false;
        return $this->tgl_kembali_rencana < now()->toDateString();
    }

    public function jumlahAlat(): int
    {
        return $this->detail()->count();
    }

    public function semuaDikembalikan(): bool
    {
        return $this->detail()->whereNull('tgl_kembali_aktual')->doesntExist();
    }
    public function daftarAlat()
	{
    		return $this->detail
      	  	->map(fn($d) => $d->alat->nama ?? '-')
        		->implode(', ');
	}

    // ---------- Auto-generate ----------

    public static function generateNoPinjam(): string
    {
        $prefix = 'PJM-' . date('Ym') . '-';
        $last   = self::where('no_pinjam', 'like', "{$prefix}%")->orderByDesc('no_pinjam')->first();
        $next   = $last ? ((int) substr($last->no_pinjam, -4)) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
