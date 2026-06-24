<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Models\PeminjamanDetail;
use App\Traits\HasActivityLog;

class Alat extends Model
{
    use SoftDeletes, HasActivityLog;

    protected $table = 'alat';

    protected $fillable = [
        'kode', 'nama', 'kategori_id', 'status',
        'lokasi', 'merk', 'no_seri', 'tgl_beli',
        'harga_beli', 'keterangan', 'foto',
    ];

    protected $casts = [
        'tgl_beli'   => 'date',
        'harga_beli' => 'decimal:2',
    ];

    // ---------- Relations ----------

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriAlat::class, 'kategori_id');
    }

	public function peminjamanDetail(): HasMany
	{
    	return $this->hasMany(PeminjamanDetail::class);
	}

	public function peminjaman()
	{
    		return $this->hasManyThrough(
        	Peminjaman::class,
        	PeminjamanDetail::class,
        	'alat_id',       // foreign key di peminjaman_detail
        	'id',            // foreign key di peminjaman
        	'id',            // local key di alat
        	'peminjaman_id'  // local key di peminjaman_detail
    		);
	}

    public function kalibrasi(): HasMany
    {
        return $this->hasMany(Kalibrasi::class);
    }

    // ---------- Scopes ----------

    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia');
    }

    public function scopeDipinjam($query)
    {
        return $query->where('status', 'dipinjam');
    }

    // ---------- Helpers ----------

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'tersedia' => '<span class="badge badge-success">Tersedia</span>',
            'dipinjam' => '<span class="badge badge-warning">Dipinjam</span>',
            'rusak'    => '<span class="badge badge-danger">Rusak</span>',
            'servis'   => '<span class="badge badge-info">Servis</span>',
            default    => '<span class="badge badge-secondary">—</span>',
        };
    }

    public function kalibrasiTerakhir(): ?Kalibrasi
    {
        return $this->kalibrasi()->latest('tgl_kalibrasi')->first();
    }

    public function isKalibrasiJatuhTempo(): bool
    {
        $last = $this->kalibrasiTerakhir();
        if (! $last) return false;
        return $last->tgl_kalibrasi_berikutnya <= now()->addDays(30)->toDateString();
    }

    // ---------- Auto-generate kode ----------

    public static function generateKode(string $kodeKategori): string
    {
        $prefix = strtoupper($kodeKategori);
        $last   = self::where('kode', 'like', "{$prefix}-%")->orderByDesc('kode')->first();
        $next   = $last ? ((int) substr($last->kode, -4)) + 1 : 1;
        return $prefix . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
