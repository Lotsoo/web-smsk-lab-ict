<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // 'created', 'updated', 'deleted', 'approved', 'rejected', 'sent'
            $table->string('entity_type'); // 'surat_masuk', 'surat_keluar', 'surat_revisi'
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('title'); // Judul aktivitas (e.g., "Surat Masuk Baru")
            $table->text('description')->nullable(); // Detail aktivitas
            $table->json('metadata')->nullable(); // Data tambahan (e.g., pengirim, nomor surat)
            $table->timestamps();

            // Indexes untuk query performance
            $table->index(['entity_type', 'entity_id']);
            $table->index(['action', 'created_at']);
            $table->index('created_at');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
