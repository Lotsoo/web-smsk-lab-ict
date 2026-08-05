<?php

// namespace App\Http\Controllers\Dashboard;

// use App\Http\Controllers\Controller;
// use App\Models\SuratRevisi;
// use Illuminate\Http\Request;

// class SuratRevisiController extends Controller
// {
//     public function index()
//     {

//         $revisiList = [
//             (object) [
//                 'id' => 1,
//                 'nomor_surat' => 'SK-001/KD/X/2025',
//                 'status' => 'ditolak',
//                 'komentar_revisi' => 'Tolong perbaiki paragraf kedua, terlalu panjang dan tidak jelas.',
//                 'file_revisi' => 'revisi/surat-keluar-1-revisi.docx',
//                 'judul_surat' => 'Undangan Rapat Koordinasi',
//                 'tanggal' => '2025-05-10',
//             ],
//             (object) [
//                 'id' => 2,
//                 'nomor_surat' => 'SK-002/KD/X/2025',
//                 'status' => 'diperbaiki',
//                 'komentar_revisi' => 'Sudah saya revisi, silakan periksa ulang.',
//                 'file_revisi' => null,
//                 'judul_surat' => 'Pemberitahuan Libur Nasional',
//                 'tanggal' => '2025-05-14',
//             ],
//         ];

//         return view('admin.dashboard.surat-revisi.view', compact('revisiList'));
//     }

//     public function create()
//     {
//         return view('admin.dashboard.surat-revisi.create');
//     }

//     public function show($id)
//     {
//         return view('admin.dashboard.surat-revisi.show', compact('id'));
//     }

//     public function update(Request $request, $id)
//     {
//         // Validasi input
//         $request->validate([
//             'komentar_revisi' => 'required|string',
//             'file_revisi' => 'nullable|file|mimes:docx,pdf|max:2048',
//         ]);

//         // Ambil data revisi yang akan diperbarui
//         $suratRevisi = \App\Models\SuratRevisi::findOrFail($id);
//         $suratRevisi->komentar_revisi = $request->komentar_revisi;

//         // Jika ada file revisi yang diunggah
//         if ($request->hasFile('file_revisi')) {
//             $file = $request->file('file_revisi');
//             $path = $file->store('revisi', 'public');
//             $suratRevisi->file_revisi = $path;
//         }

//         $suratRevisi->save();

//         // Setelah revisi disimpan, update juga data surat_keluar terkait
//         $suratKeluar = \App\Models\SuratKeluar::findOrFail($suratRevisi->surat_id);

//         // Misalnya kita update kolom status dan dokumen revisi
//         $suratKeluar->status_revisi = 'selesai'; // Atau 'direvisi'
//         if (isset($path)) {
//             $suratKeluar->dokumen_revisi_path = $path;
//         }

//         $suratKeluar->save();

//         return redirect()->route('surat-revisi')->with('success', 'Revisi surat berhasil diperbarui dan data surat keluar telah diupdate.');
//     }

//     public function edit($id)
//     {
//         $revisi = SuratRevisi::findOrFail($id);
//         return view('admin.dashboard.surat-revisi.edit', compact('revisi'));
//     }

// }




namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SuratRevisi;
use App\Models\SuratKeluar;
use App\Models\KomentarRevisi;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SuratRevisiController extends Controller
{
    // public function index()
    // {
    //     $revisiList = SuratRevisi::with(['surat', 'komentar'])->latest()->get();
    //     dd($revisiList);

    //     return view('admin.dashboard.surat-revisi.view', compact('revisiList'));
    // }
    // public function index()
    // {
    //     $revisiList = SuratRevisi::with('surat')->latest()->get();

    //     $suratIds = $revisiList->pluck('surat_id');

    //     $commentList = KomentarRevisi::with('surat')
    //         ->whereIn('surat_id', $suratIds)
    //         ->latest()
    //         ->get();

    //     dd($commentList, $revisiList);

    //     return view('admin.dashboard.surat-revisi.view', compact('revisiList', 'commentList'));
    // }

    public function index()
{
    $revisiList = SuratRevisi::with('surat')->latest()->get();

    foreach ($revisiList as $revisi) {
        $latestKomentar = KomentarRevisi::where('surat_id', $revisi->surat_id)
            ->latest()
            ->first();

        if ($latestKomentar) {
            $revisi->komentar_revisi = $latestKomentar->komentar;
        }
    }


    return view('admin.dashboard.surat-revisi.view', compact('revisiList'));
}

    public function create()
    {
        return view('admin.dashboard.surat-revisi.create');
    }

    public function show($id)
    {
        $revisi = SuratRevisi::with('surat')->findOrFail($id);
        return view('admin.dashboard.surat-revisi.show', compact('revisi'));
    }

    // public function edit($id)
    // {
    //     $revisi = SuratRevisi::with('surat')->findOrFail($id);
    //     return view('admin.dashboard.surat-revisi.edit', compact('revisi'));
    // }

    public function edit($id)
    {
        $surat = SuratKeluar::findOrFail($id);


        return view('admin.dashboard.surat-revisi.edit', compact('surat'));
    }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'nomor_surat' => 'required|string',
    //         'perihal' => 'required|string',
    //         'tujuan' => 'required|string',
    //         'isi' => 'required|string',
    //     ]);

    //     $suratRevisi = SuratRevisi::findOrFail($id);
    //     $surat = SuratKeluar::findOrFail($suratRevisi->surat_id);

    //     $surat->update([
    //         'nomor_surat' => $request->nomor_surat,
    //         'perihal' => $request->perihal,
    //         'tujuan' => $request->tujuan,
    //         'isi' => $request->isi,
    //         'status' => 'diajukan',
    //     ]);

    //     $suratRevisi->delete();

    //     return redirect()->route('surat-revisi')->with('success', 'Surat berhasil direvisi dan diajukan ulang.');
    // }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_surat' => 'required',
            'perihal' => 'required',
            'tanggal' => 'required|date',
            'isi_surat' => 'required',
        ]);

        $surat = SuratKeluar::findOrFail($id);

        $data = [
            'jenis' => $request->jenis_surat,
            'perihal' => $request->perihal,
            'tanggal' => $request->tanggal,
            'isi' => $request->isi_surat,
            'status' => 'diperbaiki',
            'keterangan_revisi' => $request->keterangan_revisi,
        ];

        // Handle specific fields based on letter type
        if ($request->jenis_surat === 'PP') {
            $data['ditujukan_kepada'] = $request->ditujukan_kepada;
            $data['jabatan_penerima'] = $request->jabatan_penerima;
            $data['jumlah_bulan'] = $request->jumlah_bulan;

            // Handle pengaju array
            if ($request->has('pengaju')) {
                $pengajuData = [];
                foreach ($request->pengaju as $pengaju) {
                    if (!empty($pengaju['nama']) && !empty($pengaju['npm'])) {
                        $pengajuData[] = [
                            'nama' => $pengaju['nama'],
                            'npm' => $pengaju['npm'],
                            'nopol' => $pengaju['nopol'] ?? '',
                            'jenis_kendaraan' => $pengaju['jenis_kendaraan'] ?? 'Motor'
                        ];
                    }
                }
                $data['pengaju'] = json_encode($pengajuData);
            }
        } elseif ($request->jenis_surat === 'SA') {
            $data['nama_kegiatan'] = $request->nama_kegiatan;
            $data['semester'] = $request->semester;
            $data['tahun_ajaran'] = $request->tahun_ajaran;

            // Handle asisten array
            if ($request->has('asisten')) {
                $asistenData = [];
                foreach ($request->asisten as $asisten) {
                    if (!empty($asisten['nama']) && !empty($asisten['npm'])) {
                        $asistenData[] = [
                            'nama' => $asisten['nama'],
                            'npm' => $asisten['npm'],
                            'matkul' => $asisten['matkul'] ?? ''
                        ];
                    }
                }
                $data['asisten'] = json_encode($asistenData);
            }
        } elseif ($request->jenis_surat === 'U') {
            $data['ditujukan_kepada'] = $request->ditujukan_kepada;
            $data['jabatan_penerima'] = $request->jabatan_penerima;
            $data['nama_kegiatan'] = $request->nama_kegiatan;
            $data['tempat_kegiatan'] = $request->tempat_kegiatan;
            $data['tanggal_kegiatan'] = $request->tanggal_kegiatan;
            $data['waktu_mulai'] = $request->waktu_mulai;
            $data['waktu_selesai'] = $request->waktu_selesai;
        }

        $surat->update($data);

        // Hapus data di SuratRevisi
        SuratRevisi::where('surat_id', $id)->delete();

        // Log activity - Revisi Completed
        ActivityLog::log(
            action: 'revisi_completed',
            entityType: 'surat_revisi',
            entityId: $id,
            title: 'Revisi Selesai',
            description: "{$request->jenis_surat} - {$request->perihal} telah direvisi dan diajukan ulang",
            metadata: [
                'nomor_surat' => $surat->nomor_surat,
                'jenis' => $request->jenis_surat,
                'perihal' => $request->perihal,
                'keterangan_revisi' => $request->keterangan_revisi,
            ]
        );

        return redirect()->route('surat-keluar')
            ->with('success', 'Surat berhasil diajukan kembali.');
    }

}
