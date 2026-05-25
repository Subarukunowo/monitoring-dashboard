<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dokumen extends Model
{
    protected $table    = 'dokumen';
    public    $timestamps = false;

    protected $fillable = [
        'id_laporan',
        'tipe',
        'nama_file',
        'path_file',
        'mime_type',
        'ukuran_file',
        'uploaded_at',  
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    // Isi uploaded_at otomatis saat create
    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->uploaded_at ??= now();
        });
    }

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class, 'id_laporan');
    }
}