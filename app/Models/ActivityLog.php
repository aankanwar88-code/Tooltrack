<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id',
        'description', 'old_values', 'new_values',
        'ip_address', 'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // ---------- Relations ----------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ---------- Helpers ----------

    public function getActionBadgeAttribute(): string
    {
        return match ($this->action) {
            'created'    => '<span class="badge badge-success">Tambah</span>',
            'updated'    => '<span class="badge badge-warning">Edit</span>',
            'deleted'    => '<span class="badge badge-danger">Hapus</span>',
            'dipinjam'   => '<span class="badge badge-primary">Pinjam</span>',
            'dikembalikan' => '<span class="badge badge-info">Kembali</span>',
            'disetujui'  => '<span class="badge badge-success">Setujui</span>',
            'ditolak'    => '<span class="badge badge-danger">Tolak</span>',
            'kalibrasi'  => '<span class="badge badge-secondary">Kalibrasi</span>',
            'login'      => '<span class="badge badge-light border">Login</span>',
            'logout'     => '<span class="badge badge-light border">Logout</span>',
            default      => '<span class="badge badge-secondary">' . ucfirst($this->action) . '</span>',
        };
    }

    public function getModelLabelAttribute(): string
    {
        return match ($this->model_type) {
            'App\\Models\\Alat'       => 'Alat',
            'App\\Models\\Peminjaman' => 'Peminjaman',
            'App\\Models\\Kalibrasi'  => 'Kalibrasi',
            'App\\Models\\User'       => 'User',
            default                   => class_basename($this->model_type),
        };
    }

    // ---------- Static factory ----------

    public static function catat(
        string  $action,
        string  $description,
        ?Model  $model      = null,
        ?array  $oldValues  = null,
        ?array  $newValues  = null,
    ): self {
        return self::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'model_type'  => $model ? get_class($model) : null,
            'model_id'    => $model?->id,
            'description' => $description,
            'old_values'  => $oldValues,
            'new_values'  => $newValues,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
        ]);
    }
}
