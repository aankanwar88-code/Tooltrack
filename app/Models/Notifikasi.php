<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $fillable = [
        'user_id', 'judul', 'pesan', 'tipe', 'icon',
        'url', 'notifiable_type', 'notifiable_id', 'dibaca_at',
    ];

    protected $casts = [
        'dibaca_at' => 'datetime',
    ];

    // ---------- Relations ----------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    // ---------- Scopes ----------

    public function scopeBelumDibaca($query)
    {
        return $query->whereNull('dibaca_at');
    }

    public function scopeUntukUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ---------- Helpers ----------

    public function sudahDibaca(): bool
    {
        return $this->dibaca_at !== null;
    }

    public function tandaiDibaca(): void
    {
        if (! $this->sudahDibaca()) {
            $this->update(['dibaca_at' => now()]);
        }
    }

    public function getWaktuAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getBadgeClassAttribute(): string
    {
        return match ($this->tipe) {
            'warning' => 'badge-warning',
            'danger'  => 'badge-danger',
            'success' => 'badge-success',
            default   => 'badge-info',
        };
    }

    public function getAlertClassAttribute(): string
    {
        return match ($this->tipe) {
            'warning' => 'alert-warning',
            'danger'  => 'alert-danger',
            'success' => 'alert-success',
            default   => 'alert-info',
        };
    }

    // ---------- Static Factory ----------

    public static function kirim(
        int    $userId,
        string $judul,
        string $pesan,
        string $tipe       = 'info',
        string $icon       = 'fas fa-bell',
        string $url        = null,
        Model  $notifiable = null,
    ): self {
        return self::create([
            'user_id'          => $userId,
            'judul'            => $judul,
            'pesan'            => $pesan,
            'tipe'             => $tipe,
            'icon'             => $icon,
            'url'              => $url,
            'notifiable_type'  => $notifiable ? get_class($notifiable) : null,
            'notifiable_id'    => $notifiable?->id,
        ]);
    }
}
