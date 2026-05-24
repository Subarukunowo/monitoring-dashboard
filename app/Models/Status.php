<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    protected $table = 'status';
    protected $fillable = ['status_kerja'];
    public $timestamps = false;

    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class, 'id_status');
    }
}