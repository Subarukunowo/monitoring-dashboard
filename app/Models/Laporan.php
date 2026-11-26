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
        'status_rab',
        'rab_catatan',
        'no_sap',
        'tanggal_selesai',
        'keterangan',
        'link_maps',
        'is_closed',
        'closed_at',
        'closed_by',
        'catatan_penutupan',
    ];

    protected $casts = [
        'tanggal_laporan' => 'date',
        'jam_laporan' => 'datetime:H:i',
        'tanggal_survei' => 'date',
        'tanggal_selesai' => 'date',
        'nilai_rab' => 'decimal:2',
        'is_closed' => 'boolean',
        'closed_at' => 'datetime',
    ];

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

    public function bqItems(): HasMany
    {
        return $this->hasMany(BqItem::class, 'id_laporan');
    }

    public function closedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function getStatusBadgeAttribute(): array
    {
        $map = [
            'pending' => ['class' => 'bg-red-100 text-red-800', 'label' => 'Terbuka'],
            'open' => ['class' => 'bg-red-100 text-red-800', 'label' => 'Terbuka'],
            'on_progress' => ['class' => 'bg-amber-100 text-amber-800', 'label' => 'Sedang Dikerjakan'],
            'on_hold' => ['class' => 'bg-orange-100 text-orange-800', 'label' => 'Ditunda'],
            'completed' => ['class' => 'bg-emerald-100 text-emerald-800', 'label' => 'Selesai'],
        ];

        $key = $this->status?->status_kerja ?? '';

        return $map[$key] ?? ['class' => 'bg-neutral-100 text-neutral-800', 'label' => 'Tidak Diketahui'];
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->tanggal_selesai !== null
            && $this->status?->status_kerja !== 'completed'
            && $this->tanggal_selesai->isPast();
    }
}
