@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section with Breadcrumb -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('surat-masuk') }}">Surat Masuk</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('surat-masuk.show', $surat->id) }}">Detail</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Review</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-primary mb-0">Review Surat Masuk</h2>
            <p class="text-muted">Tinjau dan berikan catatan pada surat masuk</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('surat-masuk.show', $surat->id) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if (session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>{{ session('status') }}</strong>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- Document Preview Column -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-file-earmark-text me-2"></i>Dokumen Surat
                    </h5>
                    <a href="{{ route('surat-masuk.download', $surat->id) }}" class="btn btn-sm btn-outline-primary" target="_blank" download>
                        <i class="bi bi-download"></i> Unduh
                    </a>
                </div>
                <div class="card-body p-3 d-flex align-items-center justify-content-center">
                    <!-- Document Preview -->
                    <div class="document-preview w-100 text-center">
                        @php
                            $extension = pathinfo($surat->dokumen_surat, PATHINFO_EXTENSION);
                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                            $isPdf = strtolower($extension) === 'pdf';
                        @endphp

                        @if($isImage)
                            <img src="{{ route('surat-masuk.view', $surat->id) }}" class="img-fluid" alt="Preview Dokumen" style="max-height: 600px;">
                        @elseif($isPdf)
                            <embed src="{{ route('surat-masuk.view', $surat->id) }}" type="application/pdf" width="100%" height="600px">
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-file-earmark-text text-primary" style="font-size: 5rem;"></i>
                                <h5 class="mt-3">{{ basename($surat->dokumen_surat) }}</h5>
                                <p class="text-muted">Klik tombol unduh untuk melihat dokumen</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Review Form Column -->
        <div class="col-lg-5">
            <!-- Letter Info Summary -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-info-circle me-2"></i>Ringkasan Surat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="bi bi-tag text-primary"></i>
                                    <span>Jenis Surat</span>
                                </div>
                                <div class="detail-value">{{ $surat->jenis_surat }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="bi bi-calendar-date text-primary"></i>
                                    <span>Tanggal</span>
                                </div>
                                <div class="detail-value">{{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d M Y') }}</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="bi bi-chat-square-text text-primary"></i>
                                    <span>Perihal</span>
                                </div>
                                <div class="detail-value">{{ $surat->perihal }}</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="bi bi-person text-primary"></i>
                                    <span>Pengirim</span>
                                </div>
                                <div class="detail-value">{{ $surat->pengirim }} {{ $surat->jabatan_pengirim ? '(' . $surat->jabatan_pengirim . ')' : '' }}</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="bi bi-building text-primary"></i>
                                    <span>Instansi</span>
                                </div>
                                <div class="detail-value">{{ $surat->instansi_pengirim ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Form -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-clipboard-check me-2"></i>Form Review
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('surat-masuk.submit-review', $surat->id) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="status_review" class="form-label fw-medium">Status Review</label>
                            <select class="form-select" id="status_review" name="status_review" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="disetujui">Disetujui</option>
                                <option value="ditolak">Ditolak</option>
                                <option value="perlu_revisi">Perlu Revisi</option>
                                <option value="tindak_lanjut">Perlu Tindak Lanjut</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="catatan_review" class="form-label fw-medium">Catatan Review</label>
                            <textarea class="form-control" id="catatan_review" name="catatan_review" rows="5"
                                placeholder="Berikan catatan, komentar, atau instruksi terkait surat ini..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="prioritas" class="form-label fw-medium">Prioritas</label>
                            <select class="form-select" id="prioritas" name="prioritas">
                                <option value="Normal">Normal</option>
                                <option value="Penting">Penting</option>
                                <option value="Segera">Segera</option>
                                <option value="Sangat Segera">Sangat Segera</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="tenggat_waktu" class="form-label fw-medium">Tenggat Waktu (Opsional)</label>
                            <input type="date" class="form-control" id="tenggat_waktu" name="tenggat_waktu">
                        </div>

                        <div class="mb-3">
                            <label for="tindak_lanjut" class="form-label fw-medium">Tindak Lanjut Oleh</label>
                            <select class="form-select" id="tindak_lanjut" name="tindak_lanjut">
                                <option value="">-- Pilih Penanggung Jawab --</option>
                                <option value="Kepala Bagian">Kepala Bagian</option>
                                <option value="Sekretaris">Sekretaris</option>
                                <option value="Bendahara">Bendahara</option>
                                <option value="Staff IT">Staff IT</option>
                                <option value="Staff Administrasi">Staff Administrasi</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i>Kirim Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Previous Reviews (if any) -->
            @if(isset($reviews) && count($reviews) > 0)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-clock-history me-2"></i>Riwayat Review
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($reviews as $review)
                        <div class="list-group-item px-4 py-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle bg-primary text-white">
                                        {{ substr($review->reviewer_name, 0, 1) }}
                                    </div>
                                    <span class="fw-bold">{{ $review->reviewer_name }}</span>
                                </div>
                                <small class="text-muted d-flex align-items-center gap-1">
                                    <i class="bi bi-clock"></i>
                                    {{ \Carbon\Carbon::parse($review->created_at)->format('d M Y, H:i') }}
                                </small>
                            </div>
                            <div class="mb-2">
                                <span class="badge {{ $review->status === 'disetujui' ? 'bg-success' : ($review->status === 'ditolak' ? 'bg-danger' : 'bg-warning') }} mb-2">
                                    {{ ucfirst($review->status) }}
                                </span>
                                <p class="mb-0">{{ $review->catatan }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* Detail Item Styling */
    .detail-item {
        margin-bottom: 0.5rem;
    }

    .detail-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
        font-size: 0.875rem;
    }

    .detail-value {
        font-weight: 500;
        font-size: 1rem;
    }

    /* Avatar Circle */
    .avatar-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
</style>
@endsection
