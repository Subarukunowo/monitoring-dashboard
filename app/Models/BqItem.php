<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BqItem extends Model
{
    protected $table = 'bq_items';

    protected $fillable = [
        'id_laporan',
        'kode_material',
        'uraian',
        'kategori',
        'satuan',
        'volume',
        'harga_satuan',
        'subtotal',
    ];

    protected $casts = [
        'volume' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class, 'id_laporan');
    }
}
