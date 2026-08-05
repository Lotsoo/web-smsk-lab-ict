<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disposisi extends Model
{
    use HasFactory;

    protected $table = 'disposisis';

    protected $fillable = [
        'surat_masuk_id',
        'tujuan',
        'catatan',
        'prioritas',
        'tenggat_waktu',
        'status',
        'created_by',
        'dibuat_oleh'
    ];

    protected $casts = [
        'tenggat_waktu' => 'date',
    ];

    public function suratMasuk()
    {
        return $this->belongsTo(SuratMasuk::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
