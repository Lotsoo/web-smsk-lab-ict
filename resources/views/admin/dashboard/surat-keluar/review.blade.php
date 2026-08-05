@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('surat-keluar') }}">Surat Keluar</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Review</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-primary mb-0">Review Surat Keluar</h2>
        </div>
        <a href="{{ route('surat-keluar') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong>{{ session('success') }}</strong>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Detail Surat -->
            <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-file-earmark-text me-2"></i>Detail Surat
                    </h5>
                    <span class="badge
                        {{ $surat->status === 'disetujui' ? 'bg-success' :
                            ($surat->status === 'ditolak' ? 'bg-danger' :
                            ($surat->status === 'sudah_mengajukan' ? 'bg-warning' :
                            ($surat->status === 'diperbaiki' ? 'bg-info' : 'bg-secondary'))) }}
                        d-flex align-items-center gap-1 px-3 py-2">
                        <i class="bi
                            {{ $surat->status === 'disetujui' ? 'bi-check-circle' :
                                ($surat->status === 'ditolak' ? 'bi-x-circle' :
                                ($surat->status === 'sudah_mengajukan' ? 'bi-send' :
                                ($surat->status === 'diperbaiki' ? 'bi-tools' : 'bi-hourglass-split'))) }}">
                        </i>
                        {{ $surat->status === 'sudah_mengajukan' ? 'Sudah Mengajukan' :
                            ($surat->status === 'diperbaiki' ? 'Sedang Diperbaiki' : ucfirst($surat->status)) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <span class="text-muted small">Nomor Surat</span>
                                <span class="fw-medium fs-5">{{ $surat->nomor_surat }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <span class="text-muted small">Tanggal</span>
                                <span class="fw-medium">{{ \Carbon\Carbon::parse($surat->tanggal)->format('d F Y') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <span class="text-muted small">Jenis Surat</span>
                                @php
                                    $kodeSurat = [
                                        'PP' => 'Pengajuan Parkir PKL',
                                        'SA' => 'Sertif Asisten / PKL',
                                        'SS' => 'Sertif Webinar / Workshop / Media Partner',
                                        'H' => 'Surat Perbaikan',
                                        'P' => 'Formulir Pendaftaran Calas',
                                        'S' => 'SK Asisten / Keterangan',
                                        'U' => 'Surat Undangan',
                                        'K' => 'Surat Keputusan',
                                        'SK' => 'Surat Keterangan',
                                        'ST' => 'Surat Tugas',
                                        'SPD' => 'Surat Perjalanan Dinas',
                                        'SU' => 'Surat Umum',
                                    ];
                                @endphp
                                <span class="fw-medium">{{ $kodeSurat[$surat->jenis] ?? $surat->jenis }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <span class="text-muted small">Perihal</span>
                                <span class="fw-medium">{{ $surat->perihal }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="text-muted small mb-2">Isi Surat</span>
                                <div class="border rounded p-3 bg-light">
                                    {!! nl2br(e($surat->isi)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lampiran / Data Pengaju -->
            @if ($lampiran->count())
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-people me-2"></i>Data Pengaju
                            <span class="badge bg-primary rounded-pill ms-2">{{ $lampiran->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-3">No</th>
                                        <th>Nama</th>
                                        <th>NIS/NPM</th>
                                        <th>No. Polisi</th>
                                        <th>Jenis Kendaraan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lampiran as $i => $item)
                                        @php
                                            $data = json_decode($item->isi, true);
                                        @endphp
                                        @if ($data)
                                            <tr>
                                                <td class="px-3">{{ $i + 1 }}</td>
                                                <td>{{ $data['nama'] ?? '-' }}</td>
                                                <td>{{ $data['npm'] ?? '-' }}</td>
                                                <td>{{ $data['nopol'] ?? '-' }}</td>
                                                <td>{{ $data['jenis_kendaraan'] ?? '-' }}</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td class="px-3">{{ $i + 1 }}</td>
                                                <td colspan="4">{{ $item->isi }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Komentar Revisi -->
            @if (isset($surat->komentarRevisi) && $surat->komentarRevisi->count())
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-chat-left-text me-2"></i>Komentar Revisi
                            <span class="badge bg-primary rounded-pill ms-2">{{ $surat->komentarRevisi->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach ($surat->komentarRevisi as $komentar)
                                <div class="list-group-item px-4 py-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-circle bg-primary text-white">
                                                {{ substr($komentar->user->name ?? 'U', 0, 1) }}
                                            </div>
                                            <span class="fw-bold">{{ $komentar->user->name ?? 'User' }}</span>
                                        </div>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>{{ $komentar->created_at->format('d M Y, H:i') }}
                                        </small>
                                    </div>
                                    <div>{{ $komentar->komentar }}</div>
                                    @if (isset($komentar->dokumen_revisi_path) && $komentar->dokumen_revisi_path)
                                        <a href="{{ Storage::url($komentar->dokumen_revisi_path) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="bi bi-file-earmark-text"></i> Lihat Dokumen Revisi
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Status -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Status Surat</h5>
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Dibuat</h6>
                                <small class="text-muted">{{ $surat->created_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker
                                {{ in_array($surat->status, ['sudah_mengajukan', 'menunggu', 'disetujui', 'ditolak', 'diperbaiki']) ? 'bg-success' : 'bg-secondary' }}">
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Diajukan</h6>
                                <small class="text-muted">
                                    {{ in_array($surat->status, ['sudah_mengajukan', 'menunggu', 'disetujui', 'ditolak', 'diperbaiki']) ?
                                       $surat->updated_at->format('d M Y, H:i') : 'Belum diajukan' }}
                                </small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker
                                {{ $surat->status === 'disetujui' ? 'bg-success' :
                                   ($surat->status === 'ditolak' ? 'bg-danger' :
                                    ($surat->status === 'diperbaiki' ? 'bg-info' : 'bg-secondary')) }}">
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-0">
                                    {{ $surat->status === 'disetujui' ? 'Disetujui' :
                                       ($surat->status === 'ditolak' ? 'Ditolak' :
                                        ($surat->status === 'diperbaiki' ? 'Sedang Diperbaiki' : 'Menunggu Persetujuan')) }}
                                </h6>
                                @if ($surat->status === 'disetujui' || $surat->status === 'ditolak' || $surat->status === 'diperbaiki')
                                    <small class="text-muted">{{ $surat->updated_at->format('d M Y, H:i') }}</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($surat->status === 'menunggu' || $surat->status === 'sudah_mengajukan' || $surat->status === 'diperbaiki')
                        <div class="mt-4">
                            <h6 class="fw-bold mb-3">Tindakan</h6>
                            <form action="{{ route('surat-keluar.approval', $surat->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="d-grid gap-2">
                                    <button type="submit" name="status" value="disetujui"
                                        class="btn btn-success" onclick="return confirm('Yakin ingin menyetujui surat ini?')">
                                        <i class="bi bi-check-circle"></i> Setujui Surat
                                    </button>
                                    <button type="submit" name="status" value="ditolak"
                                        class="btn btn-danger" onclick="return confirm('Yakin ingin menolak surat ini?')">
                                        <i class="bi bi-x-circle"></i> Tolak Surat
                                    </button>
                                </div>
                            </form>
                        </div>
                    @elseif ($surat->status !== 'disetujui')
                        <div class="alert alert-{{ $surat->status === 'ditolak' ? 'danger' :
                                                  ($surat->status === 'draft' ? 'secondary' : 'warning') }}
                                    mt-4 d-flex align-items-center flex-wrap">
                            <i class="bi {{ $surat->status === 'ditolak' ? 'bi-x-circle-fill' :
                                           ($surat->status === 'draft' ? 'bi-pencil-fill' : 'bi-hourglass-split') }} me-2"></i>
                            <span>
                                Surat ini
                                @if($surat->status === 'ditolak')
                                    telah <strong>ditolak</strong>
                                @elseif($surat->status === 'draft')
                                    masih berstatus <strong>draft</strong>
                                @elseif($surat->status === 'dicetak')
                                    telah <strong>dicetak</strong>
                                @else
                                    berstatus <strong>{{ $surat->status }}</strong>
                                @endif

                                @if ($surat->status === 'ditolak' && isset($surat->alasan_ditolak) && $surat->alasan_ditolak)
                                    <span class="d-block mt-1">Alasan: {{ $surat->alasan_ditolak }}</span>
                                @endif
                            </span>
                        </div>
                    @else
                        <div class="alert alert-success mt-4 d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <span>Surat ini telah <strong>disetujui</strong></span>
                        </div>
                    @endif

                    @if ($surat->status === 'disetujui')
                        <div class="mt-3">
                            <a href="{{ route('surat-keluar.print', $surat->id) }}" class="btn btn-primary w-100">
                                <i class="bi bi-printer"></i> Cetak Surat
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Form Komentar -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-chat-right-dots me-2"></i>Tambah Komentar
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        // dd($surat);
                    @endphp
                    <form action="{{ route('surat-keluar.comment', $surat->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="komentar" class="form-label">Komentar</label>
                            <textarea name="komentar" id="komentar" rows="4" class="form-control" required placeholder="Tulis komentar atau catatan revisi..." required></textarea>
                        </div>
                        {{-- <div class="mb-3">
                            <label for="dokumen_revisi" class="form-label">Dokumen Revisi (Opsional)</label>
                            <input type="file" name="dokumen_revisi" id="dokumen_revisi" class="form-control" accept=".pdf,.doc,.docx">
                            <div class="form-text">Format: PDF, DOC, DOCX (Maks. 2MB)</div>
                        </div> --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> Kirim Komentar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline::before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 9px;
    width: 2px;
    background-color: #e9ecef;
}
.timeline-item {
    position: relative;
    margin-bottom: 20px;
}
.timeline-marker {
    position: absolute;
    left: -30px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}
.timeline-content {
    padding-bottom: 10px;
}
.timeline-item:last-child .timeline-content {
    padding-bottom: 0;
}
</style>
@endsection
