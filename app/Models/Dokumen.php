<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dokumen extends Model
{
    protected $table = 'dokumen';
    protected $fillable = [
        'id_laporan', 'tipe', 'nama_file', 'path_file', 
        'mime_type', 'ukuran_file'
    ];
    public $timestamps = false;
    protected $casts = ['uploaded_at' => 'datetime'];

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class, 'id_laporan');
    }
}