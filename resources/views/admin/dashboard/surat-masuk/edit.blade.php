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
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-primary mb-0">Edit Surat Masuk</h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('surat-masuk') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>
                <strong>Terjadi kesalahan!</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- Document Preview Column -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-file-earmark-text me-2"></i>Dokumen Surat
                    </h5>
                </div>
                <div class="card-body p-3">
                    <!-- Current Document Preview -->
                    <div class="document-preview w-100 text-center mb-4">
                        @php
                            $extension = pathinfo($surat->dokumen_surat, PATHINFO_EXTENSION);
                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                            $isPdf = strtolower($extension) === 'pdf';
                        @endphp

                        @if($isImage)
                            <img src="{{ route('surat-masuk.view', $surat->id) }}" class="img-fluid" alt="Preview Dokumen" style="max-height: 300px;">
                        @elseif($isPdf)
                            <div class="text-center py-3">
                                <i class="bi bi-file-earmark-pdf text-danger" style="font-size: 4rem;"></i>
                                <h6 class="mt-2">{{ basename($surat->dokumen_surat) }}</h6>
                                <a href="{{ route('surat-masuk.view', $surat->id) }}" class="btn btn-sm btn-outline-primary mt-2" target="_blank">
                                    <i class="bi bi-eye"></i> Lihat PDF
                                </a>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="bi bi-file-earmark-text text-primary" style="font-size: 4rem;"></i>
                                <h6 class="mt-2">{{ basename($surat->dokumen_surat) }}</h6>
                                <a href="{{ route('surat-masuk.download', $surat->id) }}" class="btn btn-sm btn-outline-primary mt-2" target="_blank" download>
                                    <i class="bi bi-download"></i> Unduh Dokumen
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Upload New Document -->
                    <form action="{{ route('surat-masuk.update-file', $surat->id) }}" method="POST" enctype="multipart/form-data" class="mt-3">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="new_file" class="form-label fw-medium">
                                <i class="bi bi-upload me-1"></i>Ganti Dokumen (Opsional)
                            </label>
                            <input type="file" class="form-control" id="new_file" name="new_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <div class="form-text">Format: PDF, DOC(X), JPG, PNG (Maks. 5MB)</div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Simpan Dokumen Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Form Column -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-pencil-square me-2"></i>Edit Informasi Surat
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('surat-masuk.update', $surat->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="jenis_surat" class="form-label fw-medium">Jenis Surat</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-tag"></i>
                                    </span>
                                    <select id="jenis_surat" name="jenis_surat" class="form-select" required>
                                        <option value="">-- Pilih Jenis Surat --</option>
                                        <option value="Peminjaman Lab" {{ $surat->jenis_surat == 'Peminjaman Lab' ? 'selected' : '' }}>Peminjaman Lab</option>
                                        <option value="Informasi Penting" {{ $surat->jenis_surat == 'Informasi Penting' ? 'selected' : '' }}>Informasi Penting</option>
                                        <option value="lainnya" {{ !in_array($surat->jenis_surat, ['Peminjaman Lab', 'Informasi Penting']) ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6" id="jenisLainnyaContainer" style="{{ !in_array($surat->jenis_surat, ['Peminjaman Lab', 'Informasi Penting']) ? 'display:block' : 'display:none' }}">
                                <label for="jenis_lainnya" class="form-label fw-medium">Jenis Surat Lainnya</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-pencil"></i>
                                    </span>
                                    <input type="text" id="jenis_lainnya" name="jenis_lainnya" class="form-control"
                                        value="{{ !in_array($surat->jenis_surat, ['Peminjaman Lab', 'Informasi Penting']) ? $surat->jenis_surat : '' }}"
                                        placeholder="Masukkan jenis surat">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="tanggal_surat" class="form-label fw-medium">Tanggal Surat</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-calendar-date"></i>
                                    </span>
                                    <input type="date" id="tanggal_surat" name="tanggal_surat" class="form-control"
                                        value="{{ $surat->tanggal_surat }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="perihal" class="form-label fw-medium">Perihal</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-chat-square-text"></i>
                                    </span>
                                    <input type="text" id="perihal" name="perihal" class="form-control"
                                        value="{{ $surat->perihal }}" placeholder="Perihal surat" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="pengirim" class="form-label fw-medium">Nama Pengirim</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="text" id="pengirim" name="pengirim" class="form-control"
                                        value="{{ $surat->pengirim }}" placeholder="Nama pengirim surat" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="jabatan_pengirim" class="form-label fw-medium">Jabatan Pengirim</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-briefcase"></i>
                                    </span>
                                    <input type="text" id="jabatan_pengirim" name="jabatan_pengirim" class="form-control"
                                        value="{{ $surat->jabatan_pengirim }}" placeholder="Jabatan pengirim">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="instansi_pengirim" class="form-label fw-medium">Instansi Pengirim</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-building"></i>
                                    </span>
                                    <input type="text" id="instansi_pengirim" name="instansi_pengirim" class="form-control" required
                                        value="{{ $surat->instansi_pengirim }}" placeholder="Nama instansi (opsional)">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="diketahui_oleh" class="form-label fw-medium">Diketahui Oleh</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-person-check"></i>
                                    </span>
                                    <input type="text" id="diketahui_oleh" name="diketahui_oleh" class="form-control"
                                        value="{{ $surat->diketahui_oleh }}" placeholder="Nama yang mengetahui">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="jabatan_diketahui" class="form-label fw-medium">Jabatan yang Mengetahui</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-briefcase"></i>
                                    </span>
                                    <input type="text" id="jabatan_diketahui" name="jabatan_diketahui" class="form-control"
                                        value="{{ $surat->jabatan_diketahui }}" placeholder="Jabatan yang mengetahui">
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('surat-masuk.show', $surat->id) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-1"></i>Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i>Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Jenis surat lainnya toggle
        const jenisSurat = document.getElementById('jenis_surat');
        const jenisLainnyaContainer = document.getElementById('jenisLainnyaContainer');
        const jenisLainnyaInput = document.getElementById('jenis_lainnya');

        jenisSurat.addEventListener('change', function() {
            if (this.value === 'lainnya') {
                jenisLainnyaContainer.style.display = 'block';
                jenisLainnyaInput.setAttribute('required', 'required');
            } else {
                jenisLainnyaContainer.style.display = 'none';
                jenisLainnyaInput.removeAttribute('required');
            }
        });

        // File preview for new uploads
        const newFileInput = document.getElementById('new_file');

        newFileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // You could add preview functionality here if needed
                console.log('New file selected:', file.name);
            }
        });
    });
</script>
@endsection
