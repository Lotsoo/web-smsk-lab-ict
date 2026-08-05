<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKeluar extends Model
{
    use HasFactory;

    protected $table = 'surat_keluar';

    protected $guarded = ['id'];

    // protected $fillable = [
    //     'nomor_surat',
    //     'perihal',
    //     'isi',
    //     'tanggal',
    //     'jenis',
    //     'status',
    //     'user_id',
    //     'signed_by',
    //     'signed_at',
    //     'barcode_path',

    //     'ditujukan_kepada',
    //     'jabatan_penerima',
    //     'jumlah_bulan',
    //     'nama_kegiatan',
    //     'semester',
    //     'tahun_ajaran',
    //     'tempat_kegiatan',
    //     'tanggal_kegiatan',
    //     'waktu_mulai',
    //     'waktu_selesai'
    // ];


    // protected $casts = [
    //     'tanggal' => 'datetime:Y-m-d',
    //     'signed_at' => 'datetime',
    //     'tanggal_kegiatan' => 'date',
    //     'waktu_mulai' => 'datetime',
    //     'waktu_selesai' => 'datetime',
    //     'pengaju' => 'array',
    // ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function signer()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    public function lampiran()
    {
        return $this->hasMany(LampiranSurat::class, 'surat_id');
    }

    public function komentar()
    {
        return $this->hasMany(KomentarRevisi::class, 'surat_id');
    }

    public function revisi()
    {
        return $this->hasMany(SuratRevisi::class, 'surat_id');
    }
}
