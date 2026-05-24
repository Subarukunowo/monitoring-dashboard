<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $table = 'areas';
    protected $fillable = ['nama_area'];
    public $timestamps = false; // created_at default dari DB

    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class, 'id_area');
    }
}