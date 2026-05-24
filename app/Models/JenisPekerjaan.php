<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisPekerjaan extends Model
{
    protected $table = 'jenis_pekerjaan';
    protected $fillable = ['nama_jenis'];
    public $timestamps = false;

    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class, 'id_jenis_pekerjaan');
    }
}