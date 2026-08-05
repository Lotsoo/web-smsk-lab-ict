<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class LampiranSurat extends Model
{
    protected $table = 'lampiran_surat';
    protected $fillable = ['surat_id', 'label', 'isi', 'urutan_grup'];

    public function surat()
    {
        return $this->belongsTo(SuratKeluar::class, 'surat_id');
    }
}
