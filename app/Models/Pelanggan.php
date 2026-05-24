<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';
    protected $fillable = ['no_pelanggan', 'nama', 'no_telepon', 'alamat', 'status_pelanggan'];
    public $timestamps = false;

    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class, 'id_pelanggan');
    }
}