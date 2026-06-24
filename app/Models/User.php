<?php

namespace App\Models;

use App\Models\Notifikasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasActivityLog;

class User extends Authenticatable
{
    use Notifiable, HasActivityLog;

    protected $fillable = [
        'name', 'email', 'password',
        'role', 'no_induk', 'departemen', 'telepon', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    // ---------- Role Helpers ----------

    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isPetugas(): bool  { return $this->role === 'petugas'; }
    public function isPeminjam(): bool { return $this->role === 'peminjam'; }

    public function canManage(): bool  { return in_array($this->role, ['admin', 'petugas']); }

    public function getRoleBadgeAttribute(): string
    {
        return match ($this->role) {
            'admin'    => '<span class="badge badge-danger">Admin</span>',
            'petugas'  => '<span class="badge badge-primary">Petugas</span>',
            'peminjam' => '<span class="badge badge-secondary">Peminjam</span>',
            default    => '<span class="badge badge-secondary">—</span>',
        };
    }

    // ---------- Relations ----------

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'peminjam_id');
    }

    public function kalibrasiDilakukan(): HasMany
    {
        return $this->hasMany(Kalibrasi::class, 'dilakukan_oleh');
    }
    // Relasi ke notifikasi
public function notifikasi(): HasMany
{
    return $this->hasMany(Notifikasi::class);
}

// Notifikasi yang belum dibaca
public function notifikasiBelumDibaca(): HasMany
{
    return $this->hasMany(Notifikasi::class)
                ->whereNull('dibaca_at')
                ->latest();
}

// Jumlah notifikasi belum dibaca (untuk badge)
public function getJumlahNotifikasiAttribute(): int
{
    return $this->notifikasiBelumDibaca()->count();
}

}
