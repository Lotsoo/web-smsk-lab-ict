<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomentarRevisi extends Model
{
    use HasFactory;

    protected $table = 'komentar_revisi';
    protected $fillable = ['surat_id', 'komentar', 'created_by'];

    public function surat()
    {
        return $this->belongsTo(SuratKeluar::class, 'surat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
