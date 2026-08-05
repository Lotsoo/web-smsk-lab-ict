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
                    <li class="breadcrumb-item active" aria-current="page">Detail</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-primary mb-0">Detail Surat Masuk</h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('surat-masuk') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            {{-- <a href="#" class="btn btn-outline-primary d-flex align-items-center gap-2" onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak
            </a> --}}
        </div>
    </div>

    <div class="row">
        <!-- Document Preview Column -->
        <div class="col-lg-5 mb-4 ">
            <div class="card shadow-sm border-0 h-50">
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
                            <img src="{{ route('surat-masuk.view', $surat->id) }}" class="img-fluid" alt="Preview Dokumen" style="max-height: 1000px;">
                        @elseif($isPdf)
                            <embed src="{{ route('surat-masuk.view', $surat->id) }}" type="application/pdf" width="100%" height="1000px">
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

        <!-- Letter Details Column -->
        <div class="col-lg-7">
            <!-- Main Information Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-info-circle me-2"></i>Informasi Surat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
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
                                    <span>Tanggal Surat</span>
                                </div>
                                <div class="detail -value">{{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d F Y') }}</div>
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
                                    <i class="bi bi-hash text-primary"></i>
                                    <span>Nama Surat</span>
                                </div>
                                <div class="detail-value">{{ $surat->nama_surat }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sender Information Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-person me-2"></i>Informasi Pengirim
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="bi bi-person text-primary"></i>
                                    <span>Nama Pengirim</span>
                                </div>
                                <div class="detail-value">{{ $surat->pengirim }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="bi bi-briefcase text-primary"></i>
                                    <span>Jabatan Pengirim</span>
                                </div>
                                <div class="detail-value">{{ $surat->jabatan_pengirim ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="bi bi-building text-primary"></i>
                                    <span>Instansi Pengirim</span>
                                </div>
                                <div class="detail-value">{{ $surat->instansi_pengirim ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="bi bi-person-check text-primary"></i>
                                    <span>Diketahui Oleh</span>
                                </div>
                                <div class="detail-value">{{ $surat->diketahui_oleh ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="bi bi-briefcase text-primary"></i>
                                    <span>Jabatan Yang Mengetahui</span>
                                </div>
                                <div class="detail-value">{{ $surat->jabatan_diketahui ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metadata Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-card-list me-2"></i>Metadata
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Tanggal Diterima</span>
                            <span class="text-muted">{{ \Carbon\Carbon::parse($surat->created_at)->format('d F Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Diinput Oleh</span>
                            <span class="text-muted">{{ $surat->created_by_name ?? 'Admin' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Terakhir Diubah</span>
                            <span class="text-muted">{{ \Carbon\Carbon::parse($surat->updated_at)->format('d F Y, H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>ID Surat</span>
                            <span class="text-muted">{{ $surat->id }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Action Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-gear me-2"></i>Tindakan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('surat-masuk.edit', $surat->id) }}" class="btn btn-warning d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-pencil-square"></i> Edit Surat
                        </a>

                        @if(auth()->user()->role === 'superadmin')
                        <a href="{{ route('surat-masuk.review', $surat->id) }}" class="btn btn-info d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-clipboard-check"></i> Review Surat
                        </a>
                        @endif

                        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#disposisiModal">
                            <i class="bi bi-send"></i> Tindak lanjut Surat
                        </button>

                        <form action="{{ route('surat-masuk.destroy', $surat->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger d-flex align-items-center justify-content-center gap-2 w-100"
                                onclick="return confirm('Yakin ingin menghapus surat ini?')">
                                <i class="bi bi-trash"></i> Hapus Surat
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Disposisi Modal -->
<div class="modal fade" id="disposisiModal" tabindex="-1" aria-labelledby="disposisiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="disposisiModalLabel">Disposisi Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('surat-masuk.disposisi', $surat->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tujuan_disposisi" class="form-label">Tujuan Disposisi</label>
                        <select class="form-select" id="tujuan_disposisi" name="tujuan_disposisi" required>
                            <option value="">-- Pilih Tujuan --</option>
                            <option value="Kepala Bagian">Kepala Bagian</option>
                            <option value="Sekretaris">Sekretaris</option>
                            <option value="Bendahara">Bendahara</option>
                            <option value="Staff IT">Staff IT</option>
                            <option value="Staff Administrasi">Staff Administrasi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="catatan_disposisi" class="form-label">Catatan</label>
                        <textarea class="form-control" id="catatan_disposisi" name="catatan_disposisi" rows="3" placeholder="Tambahkan catatan disposisi..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="prioritas" class="form-label">Prioritas</label>
                        <select class="form-select" id="prioritas" name="prioritas">
                            <option value="Normal">Normal</option>
                            <option value="Penting">Penting</option>
                            <option value="Segera">Segera</option>
                            <option value="Sangat Segera">Sangat Segera</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tenggat_waktu" class="form-label">Tenggat Waktu (Opsional)</label>
                        <input type="date" class="form-control" id="tenggat_waktu" name="tenggat_waktu">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Disposisi</button>
                </div>
            </form>
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

    /* Print Styles */
    @media print {
        .btn, .card-header, nav, .breadcrumb, .modal, .no-print {
            display: none !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .card-body {
            padding: 0 !important;
        }

        body {
            font-size: 12pt;
        }

        .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
        }

        .detail-label {
            color: #000 !important;
        }
    }
</style>
@endsection
