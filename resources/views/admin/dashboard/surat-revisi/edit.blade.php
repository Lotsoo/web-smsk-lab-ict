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
                    <li class="breadcrumb-item active" aria-current="page">Revisi Surat</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-primary mb-0">Revisi Surat</h2>
            <p class="text-muted">Perbaiki data surat yang ditolak untuk diajukan kembali</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('surat-revisi') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Alert for Revision Comments -->
    @if ($surat->komentar_revisi)
    <div class="alert alert-info d-flex" role="alert">
        <div class="flex-shrink-0">
            <i class="bi bi-info-circle-fill fs-4 me-2"></i>
        </div>
        <div>
            <h5 class="alert-heading">Komentar Penolakan</h5>
            <p class="mb-0">{{ $surat->komentar_revisi }}</p>
        </div>
    </div>
    @endif

    <!-- Form Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="bi bi-pencil-square me-2"></i>Form Revisi Surat
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('surat-revisi.update', $surat->id) }}" method="POST" class="row g-3" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="col-md-6">
                    <label for="jenis_surat" class="form-label fw-medium">Jenis Surat</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-envelope-paper"></i>
                        </span>
                        <select id="jenis_surat" name="jenis_surat" class="form-select" required>
                            <option value="">-- Pilih Jenis Surat --</option>
                            <option value="SA" {{ $surat->jenis == 'SA' ? 'selected' : '' }}>Sertif Asisten / PKL</option>
                            <option value="SS" {{ $surat->jenis == 'SS' ? 'selected' : '' }}>Sertif Webinar / Workshop / Media Partner</option>
                            <option value="H" {{ $surat->jenis == 'H' ? 'selected' : '' }}>Surat Perbaikan</option>
                            <option value="P" {{ $surat->jenis == 'P' ? 'selected' : '' }}>Formulir Pendaftaran Calas</option>
                            <option value="S" {{ $surat->jenis == 'S' ? 'selected' : '' }}>SK Asisten / Keterangan</option>
                            <option value="U" {{ $surat->jenis == 'U' ? 'selected' : '' }}>Surat Undangan</option>
                            <option value="K" {{ $surat->jenis == 'K' ? 'selected' : '' }}>Surat Keputusan</option>
                            <option value="PP" {{ $surat->jenis == 'PP' ? 'selected' : '' }}>Pengajuan Parkir PKL</option>
                        </select>
                    </div>
                    @error('jenis_surat')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="perihal" class="form-label fw-medium">Perihal Surat</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-chat-square-text"></i>
                        </span>
                        <input type="text" name="perihal" id="perihal" class="form-control" value="{{ old('perihal', $surat->perihal) }}" placeholder="Perihal Surat" required>
                    </div>
                    @error('perihal')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="nomor_surat" class="form-label fw-medium">Nomor Surat</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-hash"></i>
                        </span>
                        <input type="text" name="nomor_surat" id="nomor_surat" class="form-control bg-light" value="{{ $surat->nomor_surat }}" readonly>
                    </div>
                    <div class="form-text">Nomor surat tidak dapat diubah</div>
                </div>

                <div class="col-md-6">
                    <label for="tanggal" class="form-label fw-medium">Tanggal Surat</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-calendar-date"></i>
                        </span>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ old('tanggal', $surat->getRawOriginal('tanggal')) }}" required>
                    </div>
                    @error('tanggal')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                    {{-- <div class="form-text">
                        Raw date value: {{ $surat->tanggal }}<br>
                        Formatted date: {{ $surat->tanggal->format('Y-m-d') }}<br>
                        Original date: {{ $surat->getRawOriginal('tanggal') }}
                    </div> --}}
                </div>

                <div id="dynamicFormFields" class="col-12">
                    @if($surat->jenis == 'PP')
                        <!-- Pengajuan Parkir PKL  -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="ditujukan_kepada" class="form-label fw-medium">Ditujukan Kepada</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="text" name="ditujukan_kepada" id="ditujukan_kepada" class="form-control" value="{{ old('ditujukan_kepada', $surat->ditujukan_kepada) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="jabatan_penerima" class="form-label fw-medium">Jabatan Penerima</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-briefcase"></i>
                                    </span>
                                    <input type="text" name="jabatan_penerima" id="jabatan_penerima" class="form-control" value="{{ old('jabatan_penerima', $surat->jabatan_penerima) }}" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label for="jumlah_bulan" class="form-label fw-medium">Jumlah Bulan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-calendar-month"></i>
                                    </span>
                                    <input type="number" name="jumlah_bulan" id="jumlah_bulan" class="form-control" min="1" max="12" value="{{ old('jumlah_bulan', $surat->jumlah_bulan ?? 3) }}" required>
                                </div>
                                <div class="form-text">Jumlah bulan pengajuan flat rate parkir</div>
                            </div>
                        </div>

                        <!-- Daftar Pengaju -->
                        @if(isset($surat->pengaju) && is_array($surat->pengaju))
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
                                        @foreach($surat->pengaju as $index => $pengaju)
                                        <tr>
                                            <td>
                                                <input type="text" name="pengaju[{{ $index }}][nama]" class="form-control" value="{{ $pengaju['nama'] ?? '' }}" required>
                                            </td>
                                            <td>
                                                <input type="text" name="pengaju[{{ $index }}][npm]" class="form-control" value="{{ $pengaju['npm'] ?? '' }}" required>
                                            </td>
                                            <td>
                                                <input type="text" name="pengaju[{{ $index }}][nopol]" class="form-control" value="{{ $pengaju['nopol'] ?? '' }}" required>
                                            </td>
                                            <td>
                                                <select name="pengaju[{{ $index }}][jenis_kendaraan]" class="form-select" required>
                                                    <option value="Motor" {{ ($pengaju['jenis_kendaraan'] ?? '') == 'Motor' ? 'selected' : '' }}>Motor</option>
                                                    <option value="Mobil" {{ ($pengaju['jenis_kendaraan'] ?? '') == 'Mobil' ? 'selected' : '' }}>Mobil</option>
                                                </select>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger" {{ $index == 0 ? 'disabled' : '' }} onclick="hapusPengaju(this)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
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
                    @elseif($surat->jenis == 'SA')
                        <!-- Sertif Asisten Fields -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nama_kegiatan" class="form-label fw-medium">Nama Kegiatan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-activity"></i>
                                    </span>
                                    <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="form-control" value="{{ old('nama_kegiatan', $surat->nama_kegiatan) }}" required>
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
                                        <option value="Ganjil" {{ old('semester', $surat->semester) == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                        <option value="Genap" {{ old('semester', $surat->semester) == 'Genap' ? 'selected' : '' }}>Genap</option>
                                    </select>
                                    <input type="text" name="tahun_ajaran" placeholder="Tahun Ajaran (cth: 2024/2025)" class="form-control" value="{{ old('tahun_ajaran', $surat->tahun_ajaran) }}" required>
                                </div>
                            </div>
                        </div>

                        <!-- Daftar Asisten -->
                        @if(isset($surat->asisten) && is_array($surat->asisten))
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
                                        @foreach($surat->asisten as $index => $asisten)
                                        <tr>
                                            <td>
                                                <input type="text" name="asisten[{{ $index }}][nama]" class="form-control" value="{{ $asisten['nama'] ?? '' }}" required>
                                            </td>
                                            <td>
                                                <input type="text" name="asisten[{{ $index }}][npm]" class="form-control" value="{{ $asisten['npm'] ?? '' }}" required>
                                            </td>
                                            <td>
                                                <input type="text" name="asisten[{{ $index }}][matkul]" class="form-control" value="{{ $asisten['matkul'] ?? '' }}" required>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger" {{ $index == 0 ? 'disabled' : '' }} onclick="hapusAsisten(this)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
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
                    @elseif($surat->jenis == 'U')
                        <!-- Surat Undangan Fields -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="ditujukan_kepada" class="form-label fw-medium">Ditujukan Kepada</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="text" name="ditujukan_kepada" id="ditujukan_kepada" class="form-control" value="{{ old('ditujukan_kepada', $surat->ditujukan_kepada) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="jabatan_penerima" class="form-label fw-medium">Jabatan Penerima</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-briefcase"></i>
                                    </span>
                                    <input type="text" name="jabatan_penerima" id="jabatan_penerima" class="form-control" value="{{ old('jabatan_penerima', $surat->jabatan_penerima) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="nama_kegiatan" class="form-label fw-medium">Nama Kegiatan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-activity"></i>
                                    </span>
                                    <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="form-control" value="{{ old('nama_kegiatan', $surat->nama_kegiatan) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="tempat_kegiatan" class="form-label fw-medium">Tempat Kegiatan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-geo-alt"></i>
                                    </span>
                                    <input type="text" name="tempat_kegiatan" id="tempat_kegiatan" class="form-control" value="{{ old('tempat_kegiatan', $surat->tempat_kegiatan) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="tanggal_kegiatan" class="form-label fw-medium">Tanggal Kegiatan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-calendar-event"></i>
                                    </span>
                                    <input type="date" name="tanggal_kegiatan" id="tanggal_kegiatan" class="form-control" value="{{ old('tanggal_kegiatan', $surat->tanggal_kegiatan) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="waktu_kegiatan" class="form-label fw-medium">Waktu Kegiatan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-clock"></i>
                                    </span>
                                    <input type="time" name="waktu_mulai" class="form-control" value="{{ old('waktu_mulai', $surat->waktu_mulai) }}" required>
                                    <span class="input-group-text">s/d</span>
                                    <input type="time" name="waktu_selesai" class="form-control" value="{{ old('waktu_selesai', $surat->waktu_selesai) }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="lampiran" class="form-label fw-medium">Lampiran (jumlah halaman)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-paperclip"></i>
                                    </span>
                                    <input type="number" name="lampiran" id="lampiran" class="form-control" min="0" value="{{ old('lampiran', $surat->lampiran ?? 0) }}" required>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Default Fields for Other Letter Types -->
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="lampiran" class="form-label fw-medium">Lampiran (jumlah halaman)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-paperclip"></i>
                                    </span>
                                    <input type="number" name="lampiran" id="lampiran" class="form-control" min="0" value="{{ old('lampiran', $surat->lampiran ?? 0) }}" required>
                                </div>
                            </div>
                        </div>

                        <!-- Lampiran Dinamis -->
                        @if(isset($surat->lampiran_data) && is_array($surat->lampiran_data))
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
                                @foreach($surat->lampiran_data as $index => $lampiran)
                                <div class="row mb-2 lampiran-group">
                                    <div class="col-md-5">
                                        <input type="text" name="lampiran_data[{{ $index }}][label]" class="form-control" placeholder="Label" value="{{ $lampiran['label'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="lampiran_data[{{ $index }}][isi]" class="form-control" placeholder="Isi" value="{{ $lampiran['isi'] ?? '' }}">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="hapusLampiran(this)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    @endif
                </div>

                <div class="col-md-12">
                    <label for="isi_surat" class="form-label fw-medium">Isi Surat</label>
                    <textarea name="isi_surat" id="isi_surat" rows="8" class="form-control" required>{{ old('isi_surat', $surat->isi) }}</textarea>
                    @error('isi_surat')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12">
                    <label for="keterangan_revisi" class="form-label fw-medium">Keterangan Revisi</label>
                    <textarea name="keterangan_revisi" id="keterangan_revisi" rows="3" class="form-control" placeholder="Jelaskan perubahan yang dilakukan...">{{ old('keterangan_revisi') }}</textarea>
                    <div class="form-text">Berikan penjelasan tentang perubahan yang telah dilakukan</div>
                </div>

                <div class="col-12 mt-4">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('surat-revisi') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </a>
                        <button type="button" id="btnPreview" class="btn btn-info text-white">
                            <i class="bi bi-eye me-1"></i>Preview
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i> Ajukan Kembali
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Original Data Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="bi bi-file-earmark-text me-2"></i>Data Surat Asli
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="bi bi-envelope-paper text-primary"></i>
                            <span>Jenis Surat</span>
                        </div>
                        <div class="detail-value">
                            @php
                                $kodeSurat = [
                                    'SA' => 'Sertif Asisten / PKL',
                                    'SS' => 'Sertif Webinar / Workshop / Media Partner',
                                    'H' => 'Surat Perbaikan',
                                    'P' => 'Formulir Pendaftaran Calas',
                                    'S' => 'SK Asisten / Keterangan',
                                    'U' => 'Surat Undangan',
                                    'K' => 'Surat Keputusan',
                                    'PP' => 'Pengajuan Parkir PKL',
                                ];
                            @endphp
                            {{ $kodeSurat[$surat->jenis] ?? $surat->jenis }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="bi bi-chat-square-text text-primary"></i>
                            <span>Perihal</span>
                        </div>
                        <div class="detail-value">{{ $surat->perihal }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="bi bi-hash text-primary"></i>
                            <span>Nomor Surat</span>
                        </div>
                        <div class="detail-value">{{ $surat->nomor_surat }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="bi bi-calendar-date text-primary"></i>
                            <span>Tanggal</span>
                        </div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($surat->tanggal)->format('d F Y') }}</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="bi bi-file-text text-primary"></i>
                            <span>Isi Surat</span>
                        </div>
                        <div class="detail-value content-box">
                            {!! nl2br(e($surat->isi)) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
            </div>
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

    .content-box {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0.375rem;
        padding: 1rem;
        white-space: pre-line;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Initialize jenis_surat change handler
        const jenisSurat = document.getElementById('jenis_surat');
        const dynamicFormFields = document.getElementById('dynamicFormFields');

        // Preview button handler
        const btnPreview = document.getElementById('btnPreview');
        const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
        const previewContent = document.getElementById('previewContent');

        btnPreview.addEventListener('click', function() {
            // Get form data
            const formData = new FormData(document.querySelector('form'));

            // Generate preview based on jenis surat
            const selectedJenis = jenisSurat.value;
            if (!selectedJenis) {
                alert('Silakan pilih jenis surat terlebih dahulu');
                return;
            }

            // Generate preview content
            let previewHtml = generatePreview(selectedJenis, formData);
            previewContent.innerHTML = previewHtml;

            // Show preview modal
            previewModal.show();
        });

        // Function to add pengaju row
        window.tambahPengaju = function() {
            const tablePengaju = document.querySelector('#tablePengaju tbody');
            const rows = tablePengaju.querySelectorAll('tr');
            const newIndex = rows.length;

            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>
                    <input type="text" name="pengaju[${newIndex}][nama]" class="form-control" required>
                </td>
                <td>
                    <input type="text" name="pengaju[${newIndex}][npm]" class="form-control" required>
                </td>
                <td>
                    <input type="text" name="pengaju[${newIndex}][nopol]" class="form-control" required>
                </td>
                <td>
                    <select name="pengaju[${newIndex}][jenis_kendaraan]" class="form-select" required>
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
        };

        // Function to remove pengaju row
        window.hapusPengaju = function(button) {
            const row = button.closest('tr');
            row.remove();
        };

        // Function to add asisten row
        window.tambahAsisten = function() {
            const tableAsisten = document.querySelector('#tableAsisten tbody');
            const rows = tableAsisten.querySelectorAll('tr');
            const newIndex = rows.length;

            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>
                    <input type="text" name="asisten[${newIndex}][nama]" class="form-control" required>
                </td>
                <td>
                    <input type="text" name="asisten[${newIndex}][npm]" class="form-control" required>
                </td>
                <td>
                    <input type="text" name="asisten[${newIndex}][matkul]" class="form-control" required>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusAsisten(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tableAsisten.appendChild(newRow);
        };

        // Function to remove asisten row
        window.hapusAsisten = function(button) {
            const row = button.closest('tr');
            row.remove();
        };

        // Function to add lampiran row
        window.tambahLampiran = function() {
            const container = document.getElementById('lampiran-container');
            const rows = container.querySelectorAll('.lampiran-group');
            const newIndex = rows.length;

            const row = document.createElement('div');
            row.className = 'row mb-2 lampiran-group';
            row.innerHTML = `
                <div class="col-md-5">
                    <div class="input-group">
                        <input type="text" name="lampiran_data[${newIndex}][label]" class="form-control" placeholder="Label">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="lampiran_data[${newIndex}][isi]" class="form-control" placeholder="Isi">
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="hapusLampiran(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(row);
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
                        <h4 style="margin-bottom: 5px;">UNIVERSITAS BUDI LUHUR</h4>
                        <h3 style="margin-top: 0; margin-bottom: 5px;">Laboratorium ICT Terpadu</h3>
                        <p style="margin-top: 0; font-size: 14px;">Jl. Ciledug Raya Petukangan Utara Jakarta Selatan</p>
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
                            <td style="text-align: right;">Jakarta, ${tanggal}</td>
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
                    while (formData.get(`lampiran_data[${lampiranCount}][label]`)) {
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

                        const label = formData.get(`lampiran_data[${lampiranCount}][label]`) || '';
                        const isi = formData.get(`lampiran_data[${lampiranCount}][isi]`) || '';

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
