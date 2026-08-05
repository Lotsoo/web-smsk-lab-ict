<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surat_keluar', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->string('perihal');
            $table->text('isi');
            $table->date('tanggal');
            $table->string('jenis');
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'diperbaiki', 'dicetak', 'draft', 'sudah_mengajukan'])->default('menunggu');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('signed_by')->nullable()->constrained('users');
            $table->timestamp('signed_at')->nullable();
            $table->string('barcode_path')->nullable();

             // Add fields for Pengajuan Parkir PKL (PP)
             $table->string('ditujukan_kepada')->nullable();
             $table->json('pengaju')->nullable()->comment('Format: [{"nama": string, "npm": string, "nopol": string, "jenis_kendaraan": string}]');
             $table->json('asisten')->nullable()->comment('Format: [{"nama": string, "npm": string, "matkul": string}]');

             $table->string('jabatan_penerima')->nullable();
             $table->integer('jumlah_bulan')->nullable();

             // Add fields for Sertifikat Asisten (SA)
             $table->string('nama_kegiatan')->nullable();
             $table->string('semester')->nullable();
             $table->string('tahun_ajaran')->nullable();

             // Add fields for Undangan (U)
             $table->string('tempat_kegiatan')->nullable();
             $table->date('tanggal_kegiatan')->nullable();
             $table->time('waktu_mulai')->nullable();
             $table->time('waktu_selesai')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_keluar');
    }
};
