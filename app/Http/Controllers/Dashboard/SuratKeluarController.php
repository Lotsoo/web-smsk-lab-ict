<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\KomentarRevisi;
use App\Models\LampiranSurat;
use App\Models\SuratKeluar;
use App\Models\SuratRevisi;
use App\Models\ActivityLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuratKeluarController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('loginError', 'Login Required!');
        }

        $dataSurat = SuratKeluar::orderBy('created_at', 'desc')->paginate(10);

        // Get next nomor surat for form
        $nextNomorSurat = $this->generateNextNomorSurat();

        // Define kode surat for display
        $kodeSurat = [
            'PP' => 'Pengajuan Parkir PKL',
            'SA' => 'Sertifikat Asisten',
            'U' => 'Undangan',
            'SK' => 'Surat Keterangan',
            'ST' => 'Surat Tugas',
            'SPD' => 'Surat Perjalanan Dinas',
            'SU' => 'Surat Umum',
        ];

        return view('admin.dashboard.surat-keluar.view', compact('dataSurat', 'nextNomorSurat', 'kodeSurat'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'jenis_surat' => 'required',
            'perihal' => 'required',
            'tanggal' => 'required|date',
            'ditujukan_kepada' => 'sometimes',
            'jabatan_penerima' => 'sometimes',
            'nama_kegiatan' => 'sometimes',
            'tempat_kegiatan' => 'sometimes',
            'tanggal_kegiatan' => 'sometimes|date',
            'waktu_mulai' => 'sometimes',
            'waktu_selesai' => 'sometimes',
            'isi_surat' => 'sometimes|string',
            'lampiran' => 'sometimes',
            'pengaju' => 'sometimes|array',
            'asisten' => 'sometimes|array',
        ], [
            'jenis_surat.required' => 'Jenis surat harus diisi',
            'perihal.required' => 'Perihal surat harus diisi',
            'tanggal.required' => 'Tanggal surat harus diisi',
            'ditujukan_kepada.required' => 'Nama penerima surat harus diisi',
            'jabatan_penerima.required' => 'Jabatan penerima surat harus diisi',
            'nama_kegiatan.required' => 'Nama kegiatan harus diisi',
            'tempat_kegiatan.required' => 'Tempat pelaksanaan kegiatan harus diisi',
            'tanggal_kegiatan.required' => 'Tanggal pelaksanaan kegiatan harus diisi',
            'waktu_mulai.required' => 'Waktu mulai kegiatan harus diisi',
            'waktu_selesai.required' => 'Waktu selesai kegiatan harus diisi',
            'isi_surat.required' => 'Isi surat harus diisi',
            'lampiran.required' => 'Lampiran surat harus diisi',
            'pengaju.required' => 'Pengaju surat harus diisi',
            'asisten.required' => 'Asisten surat harus diisi',
            'ditujukan_kepada.required' => 'Nama penerima surat harus diisi',
            'jabatan_penerima.required' => 'Jabatan penerima surat harus diisi',
        ]);

        // dd($request->pengaju);


        $bulan = date('m', strtotime($request->tanggal));
        $tahun = date('y', strtotime($request->tanggal));

        $count = SuratKeluar::whereYear('tanggal', date('Y', strtotime($request->tanggal)))->count();
        $urutan = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        $nomorSurat = "$request->jenis_surat/UBL/010/$urutan/$bulan/$tahun";

        $status = 'menunggu';

        if ($request->aksi === 'simpan_draft') {
            $status = 'draft';
        } else if ($request->aksi === 'ajukan') {
            $status = 'sudah_mengajukan';
        } else if ($request->aksi === 'langsung_cetak') {
            $status = 'disetujui';
        }

        // Create base letter data
        $suratData = [
            'nomor_surat' => $nomorSurat,
            'perihal' => $request->perihal,
            'isi' => $request->isi_surat,
            'tanggal' => $request->tanggal,
            'jenis' => $request->jenis_surat,
            'status' => $status,
            'user_id' => auth()->id(),
        ];

        // Add additional fields based on letter type
        switch ($request->jenis_surat) {
            case 'PP': // Pengajuan Parkir PKL
                if ($request->has('ditujukan_kepada')) {
                    $suratData['ditujukan_kepada'] = $request->ditujukan_kepada;
                }
                if ($request->has('jabatan_penerima')) {
                    $suratData['jabatan_penerima'] = $request->jabatan_penerima;
                }
                if ($request->has('jumlah_bulan')) {
                    $suratData['jumlah_bulan'] = $request->jumlah_bulan;
                }
                if ($request->has('pengaju')) {
                    $suratData['pengaju'] = json_encode($request->pengaju);
                }
                break;

            case 'SA': // Sertif Asisten
                if ($request->has('nama_kegiatan')) {
                    $suratData['nama_kegiatan'] = $request->nama_kegiatan;
                }
                if ($request->has('semester')) {
                    $suratData['semester'] = $request->semester;
                }
                if ($request->has('tahun_ajaran')) {
                    $suratData['tahun_ajaran'] = $request->tahun_ajaran;
                }
                if ($request->has('asisten')) {
                    $suratData['asisten'] = json_encode($request->asisten);
                }

                break;

            case 'U': // Undangan
                if ($request->has('ditujukan_kepada')) {
                    $suratData['ditujukan_kepada'] = $request->ditujukan_kepada;
                }
                if ($request->has('jabatan_penerima')) {
                    $suratData['jabatan_penerima'] = $request->jabatan_penerima;
                }
                if ($request->has('nama_kegiatan')) {
                    $suratData['nama_kegiatan'] = $request->nama_kegiatan;
                }
                if ($request->has('tempat_kegiatan')) {
                    $suratData['tempat_kegiatan'] = $request->tempat_kegiatan;
                }
                if ($request->has('tanggal_kegiatan')) {
                    $suratData['tanggal_kegiatan'] = $request->tanggal_kegiatan;
                }
                if ($request->has('waktu_mulai')) {
                    $suratData['waktu_mulai'] = $request->waktu_mulai;
                }
                if ($request->has('waktu_selesai')) {
                    $suratData['waktu_selesai'] = $request->waktu_selesai;
                }
                break;
        }

        // dd($suratData);

        // Simpan ke surat_keluar
        $surat = SuratKeluar::create($suratData);

        // Log activity based on status
        $action = $status === 'draft' ? 'created' : 'submitted';
        $title = $status === 'draft' ? 'Draft Surat Keluar Dibuat' : 'Surat Keluar Diajukan';
        $description = $status === 'draft'
            ? "Draft {$request->jenis_surat} - {$request->perihal}"
            : "{$request->jenis_surat} diajukan ke {$request->ditujukan_kepada} - {$request->perihal}";

        ActivityLog::log(
            action: $action,
            entityType: 'surat_keluar',
            entityId: $surat->id,
            title: $title,
            description: $description,
            metadata: [
                'nomor_surat' => $nomorSurat,
                'jenis' => $request->jenis_surat,
                'perihal' => $request->perihal,
                'ditujukan_kepada' => $request->ditujukan_kepada ?? null,
                'status' => $status,
            ]
        );

        switch ($request->jenis_surat) {
            case 'PP':
                if ($request->has('pengaju') && is_array($request->pengaju)) {
                    foreach ($request->pengaju as $index => $pengaju) {
                        LampiranSurat::create([
                            'surat_id' => $surat->id,
                            'label' => 'Pengaju ' . ($index + 1),
                            'isi' => json_encode($pengaju),
                            'urutan_grup' => $index + 1,
                        ]);
                    }
                }
                break;

            case 'SA':
                if ($request->has('asisten') && is_array($request->asisten)) {
                    foreach ($request->asisten as $index => $asisten) {
                        LampiranSurat::create([
                            'surat_id' => $surat->id,
                            'label' => 'Asisten ' . ($index + 1),
                            'isi' => json_encode($asisten),
                            'urutan_grup' => $index + 1,
                        ]);
                    }
                }
                break;

            case 'U':
                if ($request->has('lampiran')) {
                    LampiranSurat::create([
                        'surat_id' => $surat->id,
                        'label' => 'Jumlah Lampiran',
                        'isi' => $request->lampiran,
                        'urutan_grup' => 1,
                    ]);
                }

                LampiranSurat::create([
                    'surat_id' => $surat->id,
                    'label' => 'Detail Kegiatan',
                    'isi' => json_encode([
                        'nama_kegiatan' => $request->nama_kegiatan,
                        'tempat_kegiatan' => $request->tempat_kegiatan,
                        'tanggal_kegiatan' => $request->tanggal_kegiatan,
                        'waktu_mulai' => $request->waktu_mulai,
                        'waktu_selesai' => $request->waktu_selesai
                    ]),
                    'urutan_grup' => 2,
                ]);

                LampiranSurat::create([
                    'surat_id' => $surat->id,
                    'label' => 'Penerima Surat',
                    'isi' => json_encode([
                        'nama' => $request->ditujukan_kepada,
                        'jabatan' => $request->jabatan_penerima
                    ]),
                    'urutan_grup' => 3,
                ]);
                break;


            default:
                // Simpan lampiran dinamis
                if ($request->has('lampiran') && is_array($request->lampiran)) {
                    foreach ($request->lampiran as $index => $item) {
                        if (!empty($item['label']) && !empty($item['isi'])) {
                            LampiranSurat::create([
                                'surat_id' => $surat->id,
                                'label' => $item['label'],
                                'isi' => $item['isi'],
                                'urutan_grup' => $index + 1,
                            ]);
                        }
                    }
                }
                break;
        }

        if ($request->aksi === 'langsung_cetak') {
            return redirect()->route('surat-keluar.print', $surat->id);
        }

        $statusMessages = [
            'draft' => 'Surat berhasil disimpan sebagai draft.',
            'sudah_mengajukan' => 'Surat berhasil diajukan untuk persetujuan.',
            'menunggu' => 'Surat berhasil disimpan dan menunggu persetujuan.',
        ];

        return redirect()->route('surat-keluar')->with('success', $statusMessages[$status]);
    }

    public function show($id)
    {
        $surat = SuratKeluar::findOrFail($id);
        return view('admin.dashboard.surat-keluar.detail', compact('surat'));
    }

    public function edit($id)
    {
        $surat = SuratKeluar::with('lampiran')->findOrFail($id);

        // Only allow editing of drafts
        if ($surat->status !== 'draft') {
            return redirect()->route('surat-keluar')->with('error', 'Hanya surat dengan status draft yang dapat diedit.');
        }

        // Define kode surat for display
        $kodeSurat = [
            'PP' => 'Pengajuan Parkir PKL',
            'SA' => 'Sertifikat Asisten',
            'U' => 'Undangan',
            'SK' => 'Surat Keterangan',
            'ST' => 'Surat Tugas',
            'SPD' => 'Surat Perjalanan Dinas',
            'SU' => 'Surat Umum',
        ];

        return view('admin.dashboard.surat-keluar.edit', compact('surat', 'kodeSurat'));
    }

    public function update(Request $request, $id)
    {
        $suratKeluar = SuratKeluar::findOrFail($id);

        // Only allow updating of drafts
        if ($suratKeluar->status !== 'draft') {
            return redirect()->route('surat-keluar')->with('error', 'Hanya surat dengan status draft yang dapat diperbarui.');
        }

        $request->validate([
            'perihal' => 'required',
            'tanggal' => 'required|date',
            'isi_surat' => 'required|string',
        ]);

        // Determine status based on action
        $status = 'draft'; // Default status

        if ($request->aksi === 'ajukan') {
            $status = 'sudah_mengajukan';
        } else if ($request->aksi === 'langsung_cetak') {
            $status = 'disetujui';
        }

        // Update basic letter information
        $updateData = [
            'perihal' => $request->perihal,
            'isi' => $request->isi_surat,
            'tanggal' => $request->tanggal,
            'status' => $status,
        ];

        // Update additional fields based on letter type
        switch ($suratKeluar->jenis) {
            case 'PP': // Pengajuan Parkir PKL
                if ($request->has('ditujukan_kepada')) {
                    $updateData['ditujukan_kepada'] = $request->ditujukan_kepada;
                }
                if ($request->has('jabatan_penerima')) {
                    $updateData['jabatan_penerima'] = $request->jabatan_penerima;
                }
                if ($request->has('jumlah_bulan')) {
                    $updateData['jumlah_bulan'] = $request->jumlah_bulan;
                }
                break;

            case 'SA': // Sertif Asisten
                if ($request->has('nama_kegiatan')) {
                    $updateData['nama_kegiatan'] = $request->nama_kegiatan;
                }
                if ($request->has('semester')) {
                    $updateData['semester'] = $request->semester;
                }
                if ($request->has('tahun_ajaran')) {
                    $updateData['tahun_ajaran'] = $request->tahun_ajaran;
                }
                break;

            case 'U': // Undangan
                if ($request->has('ditujukan_kepada')) {
                    $updateData['ditujukan_kepada'] = $request->ditujukan_kepada;
                }
                if ($request->has('jabatan_penerima')) {
                    $updateData['jabatan_penerima'] = $request->jabatan_penerima;
                }
                if ($request->has('nama_kegiatan')) {
                    $updateData['nama_kegiatan'] = $request->nama_kegiatan;
                }
                if ($request->has('tempat_kegiatan')) {
                    $updateData['tempat_kegiatan'] = $request->tempat_kegiatan;
                }
                if ($request->has('tanggal_kegiatan')) {
                    $updateData['tanggal_kegiatan'] = $request->tanggal_kegiatan;
                }
                if ($request->has('waktu_mulai')) {
                    $updateData['waktu_mulai'] = $request->waktu_mulai;
                }
                if ($request->has('waktu_selesai')) {
                    $updateData['waktu_selesai'] = $request->waktu_selesai;
                }
                break;
        }

        $suratKeluar->update($updateData);

        // Log activity
        $action = $status === 'draft' ? 'updated' : 'resubmitted';
        $title = $status === 'draft' ? 'Draft Surat Keluar Diperbarui' : 'Surat Keluar Diajukan Ulang';
        $description = $status === 'draft'
            ? "Draft {$suratKeluar->jenis} - {$request->perihal} diperbarui"
            : "{$suratKeluar->jenis} diajukan ulang ke {$request->ditujukan_kepada} - {$request->perihal}";

        ActivityLog::log(
            action: $action,
            entityType: 'surat_keluar',
            entityId: $suratKeluar->id,
            title: $title,
            description: $description,
            metadata: [
                'nomor_surat' => $suratKeluar->nomor_surat,
                'jenis' => $suratKeluar->jenis,
                'perihal' => $request->perihal,
                'ditujukan_kepada' => $request->ditujukan_kepada ?? null,
                'status' => $status,
            ]
        );

        // Delete existing lampiran
        LampiranSurat::where('surat_id', $suratKeluar->id)->delete();

        // Process additional data based on letter type
        switch ($suratKeluar->jenis) {
            case 'PP': // Pengajuan Parkir PKL
                if ($request->has('pengaju') && is_array($request->pengaju)) {
                    foreach ($request->pengaju as $index => $pengaju) {
                        if (!empty($pengaju['nama']) && !empty($pengaju['npm'])) {
                            LampiranSurat::create([
                                'surat_id' => $suratKeluar->id,
                                'label' => 'Pengaju ' . ($index + 1),
                                'isi' => json_encode($pengaju),
                                'urutan_grup' => $index + 1,
                            ]);
                        }
                    }
                }
                break;

            case 'SA': // Sertif Asisten
                if ($request->has('asisten') && is_array($request->asisten)) {
                    foreach ($request->asisten as $index => $asisten) {
                        if (!empty($asisten['nama']) && !empty($asisten['npm'])) {
                            LampiranSurat::create([
                                'surat_id' => $suratKeluar->id,
                                'label' => 'Asisten ' . ($index + 1),
                                'isi' => json_encode($asisten),
                                'urutan_grup' => $index + 1,
                            ]);
                        }
                    }
                }
                break;

            default:
                // Simpan lampiran dinamis
                if ($request->has('lampiran') && is_array($request->lampiran)) {
                    foreach ($request->lampiran as $index => $item) {
                        if (!empty($item['label']) && !empty($item['isi'])) {
                            LampiranSurat::create([
                                'surat_id' => $suratKeluar->id,
                                'label' => $item['label'],
                                'isi' => $item['isi'],
                                'urutan_grup' => $index + 1,
                            ]);
                        }
                    }
                }
                break;
        }

        if ($request->aksi === 'langsung_cetak') {
            return redirect()->route('surat-keluar.print', $suratKeluar->id);
        }

        $statusMessages = [
            'draft' => 'Draft surat berhasil diperbarui.',
            'sudah_mengajukan' => 'Surat berhasil diajukan untuk persetujuan.',
            'disetujui' => 'Surat berhasil disetujui dan siap dicetak.',
        ];

        return redirect()->route('surat-keluar')->with('success', $statusMessages[$status]);
    }

    public function destroy($id)
    {
        $surat = SuratKeluar::findOrFail($id);

        // Store data for logging before deletion
        $logData = [
            'nomor_surat' => $surat->nomor_surat,
            'jenis' => $surat->jenis,
            'perihal' => $surat->perihal,
            'ditujukan_kepada' => $surat->ditujukan_kepada,
            'status' => $surat->status,
        ];

        // Delete associated lampiran
        LampiranSurat::where('surat_id', $surat->id)->delete();

        // Delete the letter
        $surat->delete();

        // Log activity
        ActivityLog::log(
            action: 'deleted',
            entityType: 'surat_keluar',
            entityId: $id,
            title: 'Surat Keluar Dihapus',
            description: "{$logData['jenis']} - {$logData['perihal']} ({$logData['nomor_surat']})",
            metadata: $logData
        );

        return redirect()->route('surat-keluar')->with('success', 'Surat berhasil dihapus.');
    }

    public function review($id)
    {
        if (auth()->user()->role !== 'superadmin') {
            return redirect()->route('surat-keluar')->with('error', 'Anda tidak memiliki akses untuk melakukan review surat.');
        }

        $surat = SuratKeluar::findOrFail($id);
        $lampiran = LampiranSurat::where('surat_id', $surat->id)->get();

        return view('admin.dashboard.surat-keluar.review', [
            'surat' => $surat,
            'lampiran' => $lampiran,
        ]);
    }

    public function cetak($id)
    {
        $surat = SuratKeluar::with('lampiran')->findOrFail($id);

        // if ($surat->status !== 'disetujui') {
        //     return redirect()->route('surat-keluar')->with('error', 'Surat belum disetujui, tidak bisa dicetak.');
        // }

        $safeNomorSurat = str_replace(['/', '\\'], '-', $surat->nomor_surat);

        $pdf = PDF::loadView('admin.dashboard.surat-keluar.print', compact('surat'));
        return $pdf->download('Surat-Keluar-' . $safeNomorSurat . '.pdf');
    }

    public function approval(Request $request, $id)
    {
        if (!auth()->user()->role === 'superadmin') {
            return redirect()->route('surat-keluar')->with('error', 'Anda tidak memiliki akses untuk melakukan approval surat.');
        }

        $surat = SuratKeluar::findOrFail($id);
        $komentar = KomentarRevisi::where('surat_id', $id)->first();

        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'komentar' => 'nullable|string',
        ]);

        $surat->status = $request->input('status');

        // If approved, set signed_by and signed_at
        if ($request->status === 'disetujui') {
            $surat->signed_by = auth()->id();
            $surat->signed_at = now();
        }

        $surat->save();

        // Log activity based on approval status
        if ($request->status === 'disetujui') {
            ActivityLog::log(
                action: 'approved',
                entityType: 'surat_keluar',
                entityId: $surat->id,
                title: 'Surat Keluar Disetujui',
                description: "{$surat->jenis} - {$surat->perihal} ({$surat->nomor_surat})",
                metadata: [
                    'nomor_surat' => $surat->nomor_surat,
                    'jenis' => $surat->jenis,
                    'perihal' => $surat->perihal,
                    'signed_by' => auth()->id(),
                ]
            );
        } else {
            ActivityLog::log(
                action: 'rejected',
                entityType: 'surat_keluar',
                entityId: $surat->id,
                title: 'Surat Keluar Ditolak',
                description: "{$surat->jenis} - {$surat->perihal} ({$surat->nomor_surat})",
                metadata: [
                    'nomor_surat' => $surat->nomor_surat,
                    'jenis' => $surat->jenis,
                    'perihal' => $surat->perihal,
                    'komentar' => $request->komentar,
                ]
            );
        }

        if ($request->status === 'ditolak') {
            SuratRevisi::create([
                'surat_id' => $surat->id,
                'nomor_surat' => $surat->nomor_surat,
                'perihal' => $surat->perihal,
                'isi' => $surat->isi,
                'status' => 'diperbaiki',
                'jenis' => $surat->jenis,
                'komentar_revisi' => $request->komentar,
                'tanggal' => $surat->tanggal,
                'created_by' => auth()->id(),
            ]);
        }

        return redirect()->route('surat-keluar.review', $id)
            ->with('success', 'Status surat berhasil diperbarui.');
    }

    public function tambahKomentar(Request $request, $id)
    {
        $request->validate([
            'komentar' => 'required|string',
            // 'dokumen_revisi' => 'nullable|file|mimes:pdf,doc,docx'
        ]);

        // $path = null;
        // if ($request->hasFile('dokumen_revisi')) {
        //     $path = $request->file('dokumen_revisi')->store('revisi_dokumen', 'public');
        // }

        KomentarRevisi::create([
            'surat_id' => $id,
            'komentar' => $request->komentar,
            'created_by' => auth()->id(),

        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    private function generateNextNomorSurat()
    {
        // Get current year and month
        $year = date('y');
        $month = date('m');

        // Get last letter number for this month
        $count = SuratKeluar::whereYear('tanggal', date('Y'))
            ->whereMonth('tanggal', date('m'))
            ->count();

        $urutan = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        // Format: jenis/UBL/010/urutan/bulan/tahun
        // Example: SA/UBL/010/001/05/25
        // We'll use a placeholder for jenis since it will be selected by the user
        return "XX/UBL/010/$urutan/$month/$year";
    }
}
