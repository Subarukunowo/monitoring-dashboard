<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    protected $table    = 'status';
    protected $fillable = ['status_kerja'];
    public    $timestamps = false;

    /**
     * Label tampilan untuk setiap nilai enum.
     * Dipakai di dropdown form create/edit.
     */
    public function getLabelAttribute(): string
    {
        return match ($this->status_kerja) {
            'open'        => 'Open',
            'on_progress' => 'Sedang Dikerjakan',
            'completed'   => 'Selesai',
            default       => ucfirst(str_replace('_', ' ', $this->status_kerja)),
        };
    }

    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class, 'id_status');
    }
}