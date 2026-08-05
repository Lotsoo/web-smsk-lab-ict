<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratRevisi extends Model
{
    use HasFactory;

    protected $table = 'surat_revisi';

    // protected $fillable = [
    //     'surat_id',
    //     'nomor_surat',
    //     'perihal',

    //     'isi',
    //     'lampiran',
    //     'tanggal',
    //     'status',
    //     'aksi',
    //     'komentar_revisi',
    //     'created_by',
    // ];
    protected $guarded = ['id'];

    public function surat()
    {
        return $this->belongsTo(SuratKeluar::class, 'surat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function komentar()
    {
        return $this->hasMany(KomentarRevisi::class, 'surat_id', 'surat_id');
    }
}
