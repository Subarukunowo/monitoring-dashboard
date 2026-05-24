<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Laporan extends Model
{
    protected $table = 'laporan';
    
    protected $fillable = [
        'no_insiden',
        'tanggal_laporan',
        'jam_laporan',
        'id_pelanggan',
        'id_area',
        'id_pengawas',
        'id_jenis_pekerjaan',
        'id_status',
        'tanggal_survei',
        'nilai_rab',
        'no_sap',
        'tanggal_selesai',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_laporan' => 'date',
        'jam_laporan' => 'datetime:H:i',
        'tanggal_survei' => 'date',
        'tanggal_selesai' => 'date',
        'nilai_rab' => 'decimal:2',
    ];

    // Relationships
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'id_area');
    }

    public function pengawas(): BelongsTo
    {
        return $this->belongsTo(Pengawas::class, 'id_pengawas');
    }

    public function jenisPekerjaan(): BelongsTo
    {
        return $this->belongsTo(JenisPekerjaan::class, 'id_jenis_pekerjaan');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'id_status');
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(Dokumen::class, 'id_laporan');
    }

    // Helper: Format status untuk UI
    public function getStatusBadgeAttribute(): array
    {
        $badges = [
            1 => ['class' => 'bg-blue-100 text-blue-800', 'label' => 'Pending'],
            2 => ['class' => 'bg-yellow-100 text-yellow-800', 'label' => 'On Progress'],
            3 => ['class' => 'bg-green-100 text-green-800', 'label' => 'Completed'],
        ];
        return $badges[$this->id_status] ?? ['class' => 'bg-gray-100 text-gray-800', 'label' => 'Unknown'];
    }
}