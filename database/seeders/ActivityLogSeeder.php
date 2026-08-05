<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use App\Models\SuratRevisi;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user pertama untuk testing
        $user = User::first();
        if (!$user) {
            return;
        }

        // Data contoh untuk Surat Masuk
        $suratMasukExamples = [
            [
                'title' => 'Surat Masuk Baru',
                'description' => 'Surat dari Department A - Undangan Rapat Koordinasi',
                'metadata' => [
                    'pengirim' => 'Department A',
                    'perihal' => 'Undangan Rapat Koordinasi',
                    'jenis_surat' => 'Undangan',
                ],
            ],
            [
                'title' => 'Surat Masuk Baru',
                'description' => 'Surat dari HRD UBL - Pemberitahuan Libur',
                'metadata' => [
                    'pengirim' => 'HRD UBL',
                    'perihal' => 'Pemberitahuan Libur Nasional',
                    'jenis_surat' => 'Pemberitahuan',
                ],
            ],
        ];

        foreach ($suratMasukExamples as $index => $example) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'created',
                'entity_type' => 'surat_masuk',
                'entity_id' => $index + 1,
                'title' => $example['title'],
                'description' => $example['description'],
                'metadata' => $example['metadata'],
                'created_at' => now()->subDays($index + 1)->subHours(rand(1, 12)),
            ]);
        }

        // Data contoh untuk Surat Keluar
        $suratKeluarExamples = [
            [
                'title' => 'Surat Keluar Terkirim',
                'description' => 'Surat Keterangan dikirim ke Department B',
                'metadata' => [
                    'nomor_surat' => 'SK/UBL/010/001/08/25',
                    'jenis' => 'SK',
                    'perihal' => 'Surat Keterangan Asisten Lab',
                    'ditujukan_kepada' => 'Department B',
                ],
            ],
            [
                'title' => 'Surat Keluar Diajukan',
                'description' => 'Pengajuan Parkir PKL diajukan ke Keuangan',
                'metadata' => [
                    'nomor_surat' => 'PP/UBL/010/002/08/25',
                    'jenis' => 'PP',
                    'perihal' => 'Pengajuan Parkir PKL',
                    'ditujukan_kepada' => 'Bagian Keuangan',
                ],
            ],
        ];

        foreach ($suratKeluarExamples as $index => $example) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'sent',
                'entity_type' => 'surat_keluar',
                'entity_id' => $index + 1,
                'title' => $example['title'],
                'description' => $example['description'],
                'metadata' => $example['metadata'],
                'created_at' => now()->subDays($index + 2)->subHours(rand(1, 12)),
            ]);
        }

        // Data contoh untuk Revisi
        $revisiExamples = [
            [
                'title' => 'Revisi Selesai',
                'description' => 'Document #1234 telah direvisi dan diajukan ulang',
                'metadata' => [
                    'nomor_surat' => 'SA/UBL/010/003/08/25',
                    'jenis' => 'SA',
                    'perihal' => 'Sertifikat Asisten',
                ],
            ],
        ];

        foreach ($revisiExamples as $index => $example) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'revisi_completed',
                'entity_type' => 'surat_revisi',
                'entity_id' => $index + 1,
                'title' => $example['title'],
                'description' => $example['description'],
                'metadata' => $example['metadata'],
                'created_at' => now()->subDays($index + 3)->subHours(rand(1, 12)),
            ]);
        }
    }
}
