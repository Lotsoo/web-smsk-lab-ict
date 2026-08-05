<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     Schema::create('surat_revisi', function (Blueprint $table) {
    //         $table->id();
    //         $table->foreignId('surat_id')->constrained('surat_keluar')->onDelete('cascade');
    //         $table->string('nomor_surat')->unique();
    //         $table->string('perihal');
    //         $table->text('isi');
    //         $table->string('lampiran')->nullable();
    //         $table->string('status');
    //         $table->string('jenis');
    //         $table->string('aksi');
    //         $table->string('tanggal')->nullable();
    //         $table->text('komentar_revisi')->nullable();
    //         $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
    //         $table->timestamps();
    //     });
    // }
    public function up(): void
    {
        Schema::create('surat_revisi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('surat_id')->constrained('surat_keluar')->onDelete('cascade');

            $table->string('nomor_surat');
            $table->string('perihal');
            $table->text('isi');
            $table->date('tanggal')->nullable();
            $table->string('jenis');
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'diperbaiki', 'dicetak', 'draft', 'sudah_mengajukan'])->default('menunggu');
            $table->text('komentar_revisi')->nullable();
            $table->string('lampiran')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }




    public function down(): void
    {
        Schema::dropIfExists('surat_revisi');
    }
};
