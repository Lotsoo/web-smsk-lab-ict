<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    protected $table = 'surat_masuk';

    protected $fillable = [
        'dokumen_surat',
        'tanggal_surat',
        'pengirim',
        'instansi_pengirim',
        'jabatan_pengirim',
        'diketahui_oleh',
        'jabatan_diketahui',
        'perihal',
        'jenis_surat',
    ];
}
