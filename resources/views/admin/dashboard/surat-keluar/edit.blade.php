@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('surat-keluar') }}">Surat Keluar</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Draft</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-primary mb-0">Edit Draft Surat Keluar</h2>
            <p class="text-muted">Edit draft surat keluar sebelum diajukan</p>
        </div>
    </div>

    <!-- Status Alert -->
    @if (session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>{{ session('status') }}</strong>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Form Edit Surat Keluar -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="bi bi-pencil-square me-2"></i>Edit Draft Surat
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('surat-keluar.update', $surat->id) }}" method="POST" id="suratKeluarForm" class="row g-3">
                @csrf
                @method('PATCH')

                <!-- Jenis Surat (Readonly) -->
                <div class="col-md-12 mb-3">
                    <label for="jenis_surat" class="form-label fw-medium">Jenis Surat</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-envelope-paper"></i>
                        </span>
                        <input type="text" class="form-control bg-light" value="{{ $kodeSurat[$surat->jenis] ?? $surat->jenis }}" readonly>
                        <input type="hidden" name="jenis_surat" value="{{ $surat->jenis }}">
                    </div>
                </div>

                <!-- Dynamic Form Fields Container -->
                <div id="dynamicFormFields" class="col-12">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Nomor Surat</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-hash"></i>
                                </span>
                                <input type="text" name="nomor_surat" class="form-control bg-light" value="{{ $surat->nomor_surat }}" readonly>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="perihal" class="form-label fw-medium">Perihal Surat</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-chat-square-text"></i>
                                </span>
                                <input type="text" name="perihal" id="perihal" class="form-control" value="{{ $surat->perihal }}" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="tanggal" class="form-label fw-medium">Tanggal Surat</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-calendar-date"></i>
                                </span>
                                <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $surat->tanggal }}" required>
                            </div>
                        </div>

                        @if($surat->jenis == 'PP')
                        <!-- Fields for Pengajuan Parkir PKL -->
                        <div class="col-md-6">
                            <label for="ditujukan_kepada" class="form-label fw-medium">Ditujukan Kepada</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" name="ditujukan_kepada" id="ditujukan_kepada" class="form-control" value="{{ $surat->ditujukan_kepada ?? 'Kepala Biro Umum' }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="jabatan_penerima" class="form-label fw-medium">Jabatan Penerima</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-briefcase"></i>
                                </span>
                                <input type="text" name="jabatan_penerima" id="jabatan_penerima" class="form-control" value="{{ $surat->jabatan_penerima ?? 'Universitas Indonesia' }}" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label for="jumlah_bulan" class="form-label fw-medium">Jumlah Bulan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-calendar-month"></i>
                                </span>
                                <input type="number" name="jumlah_bulan" id="jumlah_bulan" class="form-control" min="1" max="12" value="{{ $surat->jumlah_bulan ?? 3 }}" required>
                            </div>
                            <div class="form-text">Jumlah bulan pengajuan flat rate parkir</div>
                        </div>
                        @endif

                        @if($surat->jenis == 'SA')
                        <!-- Fields for Sertif Asisten -->
                        <div class="col-md-6">
                            <label for="nama_kegiatan" class="form-label fw-medium">Nama Kegiatan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-activity"></i>
                                </span>
                                <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="form-control" value="{{ $surat->nama_kegiatan }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="semester" class="form-label fw-medium">Semester</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-calendar3"></i>
                                </span>
                                <select name="semester" id="semester" class="form-select" required>
                                    <option value="">-- Pilih Semester --</option>
                                    <option value="Ganjil" {{ $surat->semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                    <option value="Genap" {{ $surat->semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                                </select>
                                <input type="text" name="tahun_ajaran" placeholder="Tahun Ajaran (cth: 2024/2025)" class="form-control" value="{{ $surat->tahun_ajaran }}" required>
                            </div>
                        </div>
                        @endif

                        @if($surat->jenis == 'U')
                        <!-- Fields for Surat Undangan -->
                        <div class="col-md-6">
                            <label for="ditujukan_kepada" class="form-label fw-medium">Ditujukan Kepada</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" name="ditujukan_kepada" id="ditujukan_kepada" class="form-control" value="{{ $surat->ditujukan_kepada }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="jabatan_penerima" class="form-label fw-medium">Jabatan Penerima</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-briefcase"></i>
                                </span>
                                <input type="text" name="jabatan_penerima" id="jabatan_penerima" class="form-control" value="{{ $surat->jabatan_penerima }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="nama_kegiatan" class="form-label fw-medium">Nama Kegiatan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-activity"></i>
                                </span>
                                <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="form-control" value="{{ $surat->nama_kegiatan }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="tempat_kegiatan" class="form-label fw-medium">Tempat Kegiatan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-geo-alt"></i>
                                </span>
                                <input type="text" name="tempat_kegiatan" id="tempat_kegiatan" class="form-control" value="{{ $surat->tempat_kegiatan }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="tanggal_kegiatan" class="form-label fw-medium">Tanggal Kegiatan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-calendar-event"></i>
                                </span>
                                <input type="date" name="tanggal_kegiatan" id="tanggal_kegiatan" class="form-control" value="{{ $surat->tanggal_kegiatan }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="waktu_kegiatan" class="form-label fw-medium">Waktu Kegiatan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-clock"></i>
                                </span>
                                <input type="time" name="waktu_mulai" class="form-control" value="{{ $surat->waktu_mulai }}" required>
                                <span class="input-group-text">s/d</span>
                                <input type="time" name="waktu_selesai" class="form-control" value="{{ $surat->waktu_selesai }}" required>
                            </div>
                        </div>
                        @endif

                        <div class="col-12">
                            <label for="isi_surat" class="form-label fw-medium">Isi Surat</label>
                            <textarea name="isi_surat" id="isi_surat" rows="5" class="form-control" required>{{ $surat->isi }}</textarea>
                        </div>

                        @if($surat->jenis == 'PP')
                        <!-- Daftar Pengaju for Pengajuan Parkir PKL -->
                        <div class="col-12 mt-3">
                            <label class="form-label fw-medium">Daftar Pengaju Flat Rate Parkir</label>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tablePengaju">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama</th>
                                            <th>NPM</th>
                                            <th>Nomor Polisi</th>
                                            <th>Jenis Kendaraan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $pengajuIndex = 0; @endphp
                                        @foreach($surat->lampiran as $index => $lampiran)
                                            @php
                                                $pengaju = json_decode($lampiran->isi, true);
                                                if (!$pengaju) continue;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <input type="text" name="pengaju[{{ $pengajuIndex }}][nama]" class="form-control" value="{{ $pengaju['nama'] ?? '' }}" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="pengaju[{{ $pengajuIndex }}][npm]" class="form-control" value="{{ $pengaju['npm'] ?? '' }}" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="pengaju[{{ $pengajuIndex }}][nopol]" class="form-control" value="{{ $pengaju['nopol'] ?? '' }}" required>
                                                </td>
                                                <td>
                                                    <select name="pengaju[{{ $pengajuIndex }}][jenis_kendaraan]" class="form-select" required>
                                                        <option value="Motor" {{ ($pengaju['jenis_kendaraan'] ?? '') == 'Motor' ? 'selected' : '' }}>Motor</option>
                                                        <option value="Mobil" {{ ($pengaju['jenis_kendaraan'] ?? '') == 'Mobil' ? 'selected' : '' }}>Mobil</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusPengaju(this)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @php $pengajuIndex++; @endphp
                                        @endforeach

                                        @if($pengajuIndex == 0)
                                        <tr>
                                            <td>
                                                <input type="text" name="pengaju[0][nama]" class="form-control" required>
                                            </td>
                                            <td>
                                                <input type="text" name="pengaju[0][npm]" class="form-control" required>
                                            </td>
                                            <td>
                                                <input type="text" name="pengaju[0][nopol]" class="form-control" required>
                                            </td>
                                            <td>
                                                <select name="pengaju[0][jenis_kendaraan]" class="form-select" required>
                                                    <option value="Motor">Motor</option>
                                                    <option value="Mobil">Mobil</option>
                                                </select>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger" disabled>
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5">
                                                <button type="button" class="btn btn-sm btn-success w-100" onclick="tambahPengaju()">
                                                    <i class="bi bi-plus-circle me-1"></i>Tambah Pengaju
                                                </button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        @endif

                        @if($surat->jenis == 'SA')
                        <!-- Daftar Asisten for Sertif Asisten -->
                        <div class="col-12 mt-3">
                            <label class="form-label fw-medium">Daftar Penerima Sertifikat</label>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tableAsisten">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama</th>
                                            <th>NPM</th>
                                            <th>Mata Kuliah/Kegiatan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $asistenIndex = 0; @endphp
                                        @foreach($surat->lampiran as $index => $lampiran)
                                            @php
                                                $asisten = json_decode($lampiran->isi, true);
                                                if (!$asisten) continue;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <input type="text" name="asisten[{{ $asistenIndex }}][nama]" class="form-control" value="{{ $asisten['nama'] ?? '' }}" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="asisten[{{ $asistenIndex }}][npm]" class="form-control" value="{{ $asisten['npm'] ?? '' }}" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="asisten[{{ $asistenIndex }}][matkul]" class="form-control" value="{{ $asisten['matkul'] ?? '' }}" required>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusAsisten(this)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @php $asistenIndex++; @endphp
                                        @endforeach

                                        @if($asistenIndex == 0)
                                        <tr>
                                            <td>
                                                <input type="text" name="asisten[0][nama]" class="form-control" required>
                                            </td>
                                            <td>
                                                <input type="text" name="asisten[0][npm]" class="form-control" required>
                                            </td>
                                            <td>
                                                <input type="text" name="asisten[0][matkul]" class="form-control" required>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger" disabled>
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4">
                                                <button type="button" class="btn btn-sm btn-success w-100" onclick="tambahAsisten()">
                                                    <i class="bi bi-plus-circle me-1"></i>Tambah Asisten
                                                </button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        @endif

                        @if($surat->jenis != 'PP' && $surat->jenis != 'SA' && $surat->jenis != 'U')
                        <!-- Lampiran Dinamis for other letter types -->
                        <div class="col-12 mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-medium mb-0">
                                    <i class="bi bi-paperclip me-1"></i>Lampiran Tambahan (Opsional)
                                </label>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="tambahLampiran()">
                                    <i class="bi bi-plus-circle me-1"></i>Tambah Baris Lampiran
                                </button>
                            </div>
                            <div id="lampiran-container">
                                @php $lampiranIndex = 0; @endphp
                                @foreach($surat->lampiran as $index => $lampiran)
                                    <div class="row mb-2 lampiran-group">
                                        <div class="col-md-5">
                                            <input type="text" name="lampiran[{{ $lampiranIndex }}][label]" class="form-control" value="{{ $lampiran->label }}" placeholder="Label">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="lampiran[{{ $lampiranIndex }}][isi]" class="form-control" value="{{ $lampiran->isi }}" placeholder="Isi">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="hapusLampiran(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @php $lampiranIndex++; @endphp
                                @endforeach

                                @if($lampiranIndex == 0)
                                <div class="row mb-2 lampiran-group">
                                    <div class="col-md-5">
                                        <input type="text" name="lampiran[0][label]" class="form-control" placeholder="Label (misal: Nama)">
                                    </div>
                                    <div class="col-md-7">
                                        <input type="text" name="lampiran[0][isi]" class="form-control" placeholder="Isi (misal: John Doe)">
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="col-12 mt-4">
                    <div class="d-flex gap-2">
                        <button type="submit" name="aksi" value="simpan_draft" class="btn btn-secondary">
                            <i class="bi bi-save me-1"></i>Simpan Draft
                        </button>
                        <button type="button" id="btnPreview" class="btn btn-info text-white">
                            <i class="bi bi-eye me-1"></i>Preview
                        </button>
                        <button type="submit" name="aksi" value="ajukan" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i>Ajukan Persetujuan
                        </button>
                        <a href="{{ route('surat-keluar') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Preview Surat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="previewContent">
                    <!-- Preview content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="btnSaveAfterPreview">Simpan Draft</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Initialize counters for dynamic tables
        window.pengajuIndex = {{ $surat->jenis == 'PP' ? max($pengajuIndex, 1) : 1 }};
        window.asistenIndex = {{ $surat->jenis == 'SA' ? max($asistenIndex, 1) : 1 }};
        window.indexLampiran = {{ $surat->jenis != 'PP' && $surat->jenis != 'SA' && $surat->jenis != 'U' ? max($lampiranIndex, 1) : 1 }};

        // Preview button handler
        const btnPreview = document.getElementById('btnPreview');
        const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
        const previewContent = document.getElementById('previewContent');

        btnPreview.addEventListener('click', function() {
            // Get form data
            const formData = new FormData(document.getElementById('suratKeluarForm'));

            // Generate preview based on jenis surat
            const jenisSurat = '{{ $surat->jenis }}';

            // Generate preview content
            let previewHtml = generatePreview(jenisSurat, formData);
            previewContent.innerHTML = previewHtml;

            // Show preview modal
            previewModal.show();
        });

        // Save after preview button handler
        const btnSaveAfterPreview = document.getElementById('btnSaveAfterPreview');

        btnSaveAfterPreview.addEventListener('click', function() {
            // Add hidden field for draft status
            const draftInput = document.createElement('input');
            draftInput.type = 'hidden';
            draftInput.name = 'aksi';
            draftInput.value = 'simpan_draft';
            document.getElementById('suratKeluarForm').appendChild(draftInput);

            // Submit form
            document.getElementById('suratKeluarForm').submit();

            // Hide modal
            bootstrap.Modal.getInstance(document.getElementById('previewModal')).hide();
        });

        // Function to add pengaju row
        window.tambahPengaju = function() {
            const tablePengaju = document.querySelector('#tablePengaju tbody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>
                    <input type="text" name="pengaju[${window.pengajuIndex}][nama]" class="form-control" required>
                </td>
                <td>
                    <input type="text" name="pengaju[${window.pengajuIndex}][npm]" class="form-control" required>
                </td>
                <td>
                    <input type="text" name="pengaju[${window.pengajuIndex}][nopol]" class="form-control" required>
                </td>
                <td>
                    <select name="pengaju[${window.pengajuIndex}][jenis_kendaraan]" class="form-select" required>
                        <option value="Motor">Motor</option>
                        <option value="Mobil">Mobil</option>
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusPengaju(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tablePengaju.appendChild(newRow);
            window.pengajuIndex++;
        };

        // Function to remove pengaju row
        window.hapusPengaju = function(button) {
            const row = button.closest('tr');
            row.remove();
        };

        // Function to add asisten row
        window.tambahAsisten = function() {
            const tableAsisten = document.querySelector('#tableAsisten tbody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>
                    <input type="text" name="asisten[${window.asistenIndex}][nama]" class="form-control" required>
                </td>
                <td>
                    <input type="text" name="asisten[${window.asistenIndex}][npm]" class="form-control" required>
                </td>
                <td>
                    <input type="text" name="asisten[${window.asistenIndex}][matkul]" class="form-control" required>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusAsisten(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tableAsisten.appendChild(newRow);
            window.asistenIndex++;
        };

        // Function to remove asisten row
        window.hapusAsisten = function(button) {
            const row = button.closest('tr');
            row.remove();
        };

        // Function to add lampiran row
        window.tambahLampiran = function() {
            const container = document.getElementById('lampiran-container');
            const row = document.createElement('div');
            row.className = 'row mb-2 lampiran-group';
            row.innerHTML = `
                <div class="col-md-5">
                    <div class="input-group">
                        <input type="text" name="lampiran[${window.indexLampiran}][label]" class="form-control" placeholder="Label">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="lampiran[${window.indexLampiran}][isi]" class="form-control" placeholder="Isi">
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="hapusLampiran(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(row);
            window.indexLampiran++;
        };

        // Function to remove lampiran row
        window.hapusLampiran = function(button) {
            const row = button.closest('.lampiran-group');
            row.remove();
        };

        // Function to generate preview based on jenis surat
        function generatePreview(jenisSurat, formData) {
            let previewHtml = '';

            // Get common form values
            const perihal = formData.get('perihal') || '';
            const nomorSurat = formData.get('nomor_surat') || '';
            const tanggal = formData.get('tanggal') ? new Date(formData.get('tanggal')).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : '';
            const isiSurat = formData.get('isi_surat') || '';

            // Header for all previews
            previewHtml += `
                <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <h4 style="margin-bottom: 5px;">UNIVERSITAS INDONESIA</h4>
                        <h3 style="margin-top: 0; margin-bottom: 5px;">FAKULTAS ILMU KOMPUTER</h3>
                        <p style="margin-top: 0; font-size: 14px;">Kampus UI Depok, Jawa Barat 16424</p>
                        <hr style="border-top: 3px solid #000; margin-top: 10px;">
                    </div>
            `;

            // Letter number and date
            previewHtml += `
                <div style="margin-bottom: 20px;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 120px;">Nomor</td>
                            <td>: ${nomorSurat}</td>
                            <td style="text-align: right;">Depok, ${tanggal}</td>
                        </tr>
                        <tr>
                            <td>Perihal</td>
                            <td colspan="2">: ${perihal}</td>
                        </tr>
                    </table>
                </div>
            `;

            // Specific content based on jenis surat
            switch(jenisSurat) {
                case 'PP': // Pengajuan Parkir PKL
                    const ditujukanKepada = formData.get('ditujukan_kepada') || '';
                    const jabatanPenerima = formData.get('jabatan_penerima') || '';
                    const jumlahBulan = formData.get('jumlah_bulan') || '';

                    // Recipient
                    previewHtml += `
                        <div style="margin-bottom: 20px;">
                            <p>Kepada Yth.<br>
                            ${ditujukanKepada}<br>
                            ${jabatanPenerima}</p>
                        </div>
                    `;

                    // Content
                    previewHtml += `
                        <div style="margin-bottom: 20px; text-align: justify;">
                            <p>${isiSurat.replace(/\n/g, '<br>')}</p>
                        </div>
                    `;

                    // Table of applicants
                    previewHtml += `
                        <div style="margin-bottom: 20px;">
                            <h4>Daftar Pengaju Flat Rate Parkir (${jumlahBulan} bulan):</h4>
                            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                                <thead>
                                    <tr style="background-color: #f2f2f2;">
                                        <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">No</th>
                                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Nama</th>
                                        <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">NPM</th>
                                        <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Nomor Polisi</th>
                                        <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Jenis Kendaraan</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    // Get all pengaju data
                    let pengajuCount = 0;
                    while (formData.get(`pengaju[${pengajuCount}][nama]`)) {
                        const nama = formData.get(`pengaju[${pengajuCount}][nama]`) || '';
                        const npm = formData.get(`pengaju[${pengajuCount}][npm]`) || '';
                        const nopol = formData.get(`pengaju[${pengajuCount}][nopol]`) || '';
                        const jenisKendaraan = formData.get(`pengaju[${pengajuCount}][jenis_kendaraan]`) || '';

                        previewHtml += `
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${pengajuCount + 1}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">${nama}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${npm}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${nopol}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${jenisKendaraan}</td>
                            </tr>
                        `;

                        pengajuCount++;
                    }

                    previewHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;
                    break;

                case 'SA': // Sertif Asisten
                    const namaKegiatan = formData.get('nama_kegiatan') || '';
                    const semester = formData.get('semester') || '';
                    const tahunAjaran = formData.get('tahun_ajaran') || '';

                    // Content
                    previewHtml += `
                        <div style="margin-bottom: 20px; text-align: justify;">
                            <p>${isiSurat.replace(/\n/g, '<br>')}</p>
                        </div>
                    `;

                    // Table of assistants
                    previewHtml += `
                        <div style="margin-bottom: 20px;">
                            <h4>Daftar Penerima Sertifikat ${namaKegiatan} Semester ${semester} ${tahunAjaran}:</h4>
                            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                                <thead>
                                    <tr style="background-color: #f2f2f2;">
                                        <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">No</th>
                                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Nama</th>
                                        <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">NPM</th>
                                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Mata Kuliah/Kegiatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    // Get all asisten data
                    let asistenCount = 0;
                    while (formData.get(`asisten[${asistenCount}][nama]`)) {
                        const nama = formData.get(`asisten[${asistenCount}][nama]`) || '';
                        const npm = formData.get(`asisten[${asistenCount}][npm]`) || '';
                        const matkul = formData.get(`asisten[${asistenCount}][matkul]`) || '';

                        previewHtml += `
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${asistenCount + 1}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">${nama}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${npm}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">${matkul}</td>
                            </tr>
                        `;

                        asistenCount++;
                    }

                    previewHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;
                    break;

                case 'U': // Surat Undangan
                    const ditujukanKepadaU = formData.get('ditujukan_kepada') || '';
                    const jabatanPenerimaU = formData.get('jabatan_penerima') || '';
                    const namaKegiatanU = formData.get('nama_kegiatan') || '';
                    const tempatKegiatan = formData.get('tempat_kegiatan') || '';
                    const tanggalKegiatan = formData.get('tanggal_kegiatan') ? new Date(formData.get('tanggal_kegiatan')).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : '';
                    const waktuMulai = formData.get('waktu_mulai') || '';
                    const waktuSelesai = formData.get('waktu_selesai') || '';

                    // Recipient
                    previewHtml += `
                        <div style="margin-bottom: 20px;">
                            <p>Kepada Yth.<br>
                            ${ditujukanKepadaU}<br>
                            ${jabatanPenerimaU}</p>
                        </div>
                    `;

                    // Content
                    previewHtml += `
                        <div style="margin-bottom: 20px; text-align: justify;">
                            <p>${isiSurat.replace(/\n/g, '<br>')}</p>
                        </div>
                    `;

                    // Event details
                    previewHtml += `
                        <div style="margin-bottom: 20px;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 120px; vertical-align: top;">Acara</td>
                                    <td style="vertical-align: top;">: ${namaKegiatanU}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">Hari/Tanggal</td>
                                    <td style="vertical-align: top;">: ${tanggalKegiatan}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">Waktu</td>
                                    <td style="vertical-align: top;">: ${waktuMulai} - ${waktuSelesai} WIB</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">Tempat</td>
                                    <td style="vertical-align: top;">: ${tempatKegiatan}</td>
                                </tr>
                            </table>
                        </div>
                    `;
                    break;

                default: // Default letter format
                    // Content
                    previewHtml += `
                        <div style="margin-bottom: 20px; text-align: justify;">
                            <p>${isiSurat.replace(/\n/g, '<br>')}</p>
                        </div>
                    `;

                    // Check if there are lampiran items
                    let lampiranCount = 0;
                    while (formData.get(`lampiran[${lampiranCount}][label]`)) {
                        if (lampiranCount === 0) {
                            previewHtml += `
                                <div style="margin-bottom: 20px;">
                                    <h4>Lampiran:</h4>
                                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                                        <thead>
                                            <tr style="background-color: #f2f2f2;">
                                                <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">No</th>
                                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Label</th>
                                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Isi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;
                        }

                        const label = formData.get(`lampiran[${lampiranCount}][label]`) || '';
                        const isi = formData.get(`lampiran[${lampiranCount}][isi]`) || '';

                        if (label && isi) {
                            previewHtml += `
                                <tr>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${lampiranCount + 1}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px;">${label}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px;">${isi}</td>
                                </tr>
                            `;
                        }

                        lampiranCount++;
                    }

                    if (lampiranCount > 0) {
                        previewHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;
                    }
                    break;
            }

            // Footer for all previews
            previewHtml += `
                <div style="margin-top: 30px; text-align: right;">
                    <p>Hormat kami,<br><br><br><br>
                    <strong>Nama Penandatangan</strong><br>
                    Jabatan</p>
                </div>
            `;

            // Close the main container
            previewHtml += `</div>`;

            return previewHtml;
        }
    });
</script>
@endsection
