<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengawas extends Model
{
    protected $table = 'pengawas';
    protected $fillable = ['nama', 'no_telepon'];
    public $timestamps = false;

    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class, 'id_pengawas');
    }
}