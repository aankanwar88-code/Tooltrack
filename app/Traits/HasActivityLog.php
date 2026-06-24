<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait HasActivityLog
{
    /**
     * Boot trait — auto-log created, updated, deleted.
     */
    public static function bootHasActivityLog(): void
    {
        static::created(function ($model) {
            ActivityLog::catat(
                action:      'created',
                description: self::buildDescription('menambahkan', $model),
                model:       $model,
                newValues:   self::filterLogFields($model->getAttributes()),
            );
        });

        static::updated(function ($model) {
            $dirty = $model->getDirty();
            if (empty($dirty)) return;

            ActivityLog::catat(
                action:      'updated',
                description: self::buildDescription('mengubah', $model),
                model:       $model,
                oldValues:   self::filterLogFields(
                                array_intersect_key($model->getOriginal(), $dirty)
                             ),
                newValues:   self::filterLogFields($dirty),
            );
        });

        static::deleted(function ($model) {
            ActivityLog::catat(
                action:      'deleted',
                description: self::buildDescription('menghapus', $model),
                model:       $model,
                oldValues:   self::filterLogFields($model->getAttributes()),
            );
        });
    }

    // ---------- Private helpers ----------

    private static function buildDescription(string $verb, $model): string
    {
        $user  = Auth::user()?->name ?? 'System';
        $label = class_basename(get_class($model));

        // Coba ambil nama/label dari model
        $name = $model->nama ?? $model->name ?? $model->no_pinjam
             ?? $model->no_kalibrasi ?? "ID #{$model->id}";

        return "{$user} {$verb} {$label}: {$name}";
    }

    private static function filterLogFields(array $attributes): array
    {
        // Jangan log kolom sensitif atau besar
        $excluded = ['password', 'remember_token', 'foto', 'dokumen', 'user_agent'];

        return array_diff_key($attributes, array_flip($excluded));
    }
}
