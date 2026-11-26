<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    protected $table = 'status';

    protected $fillable = ['status_kerja'];

    public $timestamps = false;

    /**
     * Label yang ditampilkan di UI (bahasa Indonesia).
     */
    public function getLabelAttribute(): string
    {
        return match ($this->status_kerja) {
            'pending', 'open' => 'Terbuka',
            'on_progress' => 'Sedang Dikerjakan',
            'on_hold' => 'Ditunda',
            'completed' => 'Selesai',
            default => ucfirst(str_replace('_', ' ', $this->status_kerja)),
        };
    }

    /**
     * CSS class badge untuk tampilan tabel.
     * Mengembalikan string Tailwind: "bg-xxx text-xxx"
     */
    public function getBadgeClassAttribute(): string
    {
        return match ($this->status_kerja) {
            'pending', 'open' => 'bg-blue-100 text-blue-800',
            'on_progress' => 'bg-yellow-100 text-yellow-800',
            'on_hold' => 'bg-orange-100 text-orange-800',
            'completed' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class, 'id_status');
    }
}
