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
        Schema::create('surat_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('dokumen_surat');
            $table->date('tanggal_surat');
            $table->string('pengirim');
            $table->string('instansi_pengirim')->nullable();
            $table->string('jabatan_pengirim');
            $table->string('diketahui_oleh');
            $table->string('jabatan_diketahui');
            $table->string('perihal');
            $table->string('jenis_surat');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_masuk');
    }
};
