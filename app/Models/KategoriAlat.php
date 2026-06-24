<?php
// app/Models/KategoriAlat.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriAlat extends Model
{
    protected $table = 'kategori_alat';
    protected $fillable = ['nama', 'kode', 'deskripsi'];

    public function alat(): HasMany
    {
        return $this->hasMany(Alat::class, 'kategori_id');
    }
}
